<?php

declare(strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;

class Ban extends Controller
{
    private $_banned_count = 0;
    private $_current_user;
    private $_users_count = 0;


    /* SUMMARY
     * 
     * constructor
     * 
     * buildPage
     * getBannedList
     * getUsersList
     * showBan
     * showDefault
     * 
     */

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load Model + Language
        parent::loadModel('adm/ban');
        parent::loadLang(['adm/global', 'adm/ban']);

        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization(
                __CLASS__, 
                (int) $this->_current_user['user_authlevel'])
        ) {
            die(Administration::noAccessMessage(
                $this->langs->line('no_permissions')
            ));
        }

        $this->buildPage();
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        $mode = $_GET['mode'];

        switch ((isset($mode) ? $mode : '')) {
            case 'ban':
                $view = $this->showBan();
                break;

            case '':
                return;

            default:
                $view = $this->showDefault();
                break;
        }

        parent::$page->displayAdmin($view);
    }

    // ---------------------------------------------------------- getBannedList

    private function getBannedList(): string
    {
        $g_ord2 = $_GET['order2'];
 
        $order = (isset($g_ord2) && $g_ord2 == 'id') ? 'user_id' : 'user_name';
        $banned_list = '';

        // Get the banned users
        $banned_query = $this->Ban_Model->getBannedUsers($order);

        foreach ($banned_query as $user) {
            $banned_list .= 
                '<option value="' 
                    . $user['user_name'] . '">' 
                    . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' 
                    . $user['user_id'] . 
                ')</option>';

            $this->_banned_count++;
        }

        // Free resources
        unset($banned_query); 

        // Return builded list
        return $banned_list; 
    }

    // ----------------------------------------------------------- getUsersList

    private function getUsersList(): string
    {
        $order = $_GET['order'];

        $query_order = (isset($order) && $order == 'id') ? 'user_id' : 'user_name';

        $where_authlevel = '';
        $where_banned    = '';
        $users_list      = '';

        if ($this->_current_user['user_authlevel'] != 3) {
            $where_authlevel = "
                WHERE `user_authlevel` < '" 
                . $this->_current_user['user_authlevel'] . "'
            ";
        }

        $view = $_GET['view'];

        if (
            isset($view) 
            && $view == 'user_banned'
        ) {

            if ($this->_current_user['user_authlevel'] == 3) {
                $where_banned = "WHERE `user_banned` <> '0'";
            } else {
                $where_banned = "AND `user_banned` <> '1'";
            }

        }

        // Get the users according to the filters
        $users_query = $this->Ban_Model->getListOfUsers($where_authlevel, 
                                                        $where_banned, 
                                                        $query_order);

        foreach ($users_query as $user) {
            $status = '';

            if ($user['user_banned'] == 1) {
                $status = $this->langs->line('bn_status');
            }

            $users_list .= 
                '<option value="' 
                    . $user['user_name'] . '">' 
                    . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' 
                    . $user['user_id'] . ')' 
                    . $status .
                '</option>';

            $this->_users_count++;
        }

        // Free resources
        unset($users_query);

        // Return builded list
        return $users_list; 
    }

    // ---------------------------------------------------------------- showBan

    private function showBan(): string
    {
        $parse = $this->langs->language;

        $parse['js_path']      = JS_PATH;
        $parse['alert']        = '';
        $parse['bn_sub_title'] = '';
        $parse['reason']       = '';

        $mode = $_GET['ban_name'];

        $ban_name = isset($mode) ? $mode : NULL;

        $lift_ban_msg = $this->langs->line('bn_auto_lift_ban_message');

        if (
            isset($_GET['banuser']) 
            && isset($ban_name)
        ) {
            $parse['name']         = $ban_name;
            $parse['banned_until'] = '';
            $parse['changedate']   = $lift_ban_msg;
            $parse['vacation']     = '';

            $ban_user = $this->Ban_Model->getBannedUserData($ban_name);

            $bn_banned_until  = $this->langs->line('bn_banned_until');
            $bn_change_date   = $this->langs->line('bn_change_date');
            $bn_edit_ban_help = $this->langs->line('bn_edit_ban_help');

            if ($ban_user) {
                $parse['banned_until'] = 
                    $bn_banned_until . ' (' .
                    date(FunctionsLib::readConfig('date_format_extended'), 
                    $ban_user['banned_longer']) .
                    ')';

                $parse['reason'] = $ban_user['banned_theme'];

                $parse['changedate'] = 
                    '<div style="float:left">' 
                        . $bn_change_date . 
                    '</div>

                    <div style="float:right">' 
                       . Administration::showPopUp($bn_edit_ban_help) . 
                    '</div>';

                $parse['vacation'] = $ban_user['preference_vacation_mode'] ? 'checked="checked"' : '';
            }

            $ban_now = $_POST['bannow'];

            if (
                isset($ban_now) 
                && $ban_now
            ) {

                if (
                    !is_numeric($_POST['days']) 
                    || !is_numeric($_POST['hour'])
                ) {
                    $parse['alert'] = Administration::saveMessage(
                        'warning', 
                        $this->langs->line('bn_all_fields_required')
                    );
                } else {
                    $reas = (string) $_POST['text'];
                    $days = (int) $_POST['days'];
                    $hour = (int) $_POST['hour'];

                    $admin_name = $this->_current_user['user_name'];
                    $admin_mail = $this->_current_user['user_email'];

                    $current_time = time();
                    $ban_time     = $days * 86400;
                    $ban_time    += $hour * 3600;

                    $vacation_mode = isset($_POST['vacat']) ?? NULL;

                    if (isset($ban_user)) {

                        if ($ban_user['banned_longer'] > time()) {
                            $ban_time += ($ban_user['banned_longer'] - time());
                        }

                    }

                    if (($ban_time + $current_time) < time()) {
                        $banned_until = $current_time;
                    } else {
                        $banned_until = $current_time + $ban_time;
                    }

                    $this->Ban_Model->setOrUpdateBan(
                        $ban_user,
                        [
                            'ban_name'         => $ban_name,
                            'ban_reason'       => $reas,
                            'ban_time'         => $current_time,
                            'ban_until'        => $banned_until,
                            'ban_author'       => $admin_name,
                            'ban_author_email' => $admin_mail,
                        ],
                        $vacation_mode
                    );

                    $bn_ban_success = $this->langs->line('bn_ban_success');
                    $parse['alert'] = Administration::saveMessage(
                        'ok', str_replace('%s', $ban_name, $bn_ban_success)
                    );
                }
            }
        } else {
            FunctionsLib::redirect('admin.php?page=ban');
        }

        return $this->getTemplate()->set('adm/ban_result_view', $parse);
    }

    // ------------------------------------------------------------ showDefault

    private function showDefault(): string
    {
        $parse = $this->langs->language;

        $parse['js_path']      = JS_PATH;
        $parse['alert']        = '';
        $parse['bn_sub_title'] = '';
        $parse['np_general']   = '';

        if (
            isset($_POST['unban_name']) 
            && $_POST['unban_name']
        ) {
            $username = $_POST['unban_name'];
            $ban_succ = $this->langs->line('bn_lift_ban_success');

            $this->Ban_Model->unbanUser($username);

            $parse['alert'] = Administration::saveMessage(
                'ok', 
                str_replace('%s', $username, $ban_succ)
            );
        }

        $parse['users_list']    = $this->getUsersList();
        $parse['banned_list']   = $this->getBannedList();
        $parse['users_amount']  = $this->_users_count;
        $parse['banned_amount'] = $this->_banned_count;

        return $this->getTemplate()->set('adm/ban_view', $parse);
    }

}