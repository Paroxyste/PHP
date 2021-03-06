<?php

declare(strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\AllianceRanksEnumerator as AllianceRanks;
use application\core\enumerators\SwitchIntEnumerator as SwitchInt;

use application\libraries\FunctionsLib;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\alliance\Ranks;

class Alliances extends Controller
{

    private $_alert_info;
    private $_alert_type;
    private $_current_user;
    private $_edit;
    private $_id;
    private $_moon;
    private $_planet;
    private $_user_query;

    private $ranks = NULL;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * buildUsersCombo
     * checkAlliance
     * getData
     * getDataInfo
     * getDataMembers
     * getDataRanks
     * saveData
     * saveInfo
     * saveMembers
     * saveRanks
     *
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/alliances');
        parent::loadLang(['adm/global', 'adm/alliances']);

        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization(
                __CLASS__, 
                (int) $this->_current_user['user_authlevel'])
        ) {
            die(
                Administration::noAccessMessage(
                    $this->langs->line('no_permissions')
                )
            );
        }

        $this->buidPage();
    }

    // -------------------------------------------------------------- buildPage

    private function buidPage(): void
    {
        $parse = $this->langs->language;
        $parse['alert'] = '';

        $g_alli = $_GET['alliance'];
        $g_type = $_GET['type'];
        $g_edit = $_GET['edit'];

        $alliance    = isset($g_alli) ? trim($g_alli) : NULL;
        $type        = isset($g_type) ? trim($g_type) : NULL;
        $this->_edit = isset($g_edit) ? trim($g_edit) : NULL;

        if ($alliance != NULL) {

            if (!$this->checkAlliance($alliance)) {
                $parse['alert'] = Administration::saveMessage(
                    'error', $this->langs->line('al_nothing_found')
                );

                $alliance = '';
            } else {
                $this->_alliance_query = $this->Alliances_Model->getAllAllianceDataById(
                    $this->_id
                );

                $this->ranks = new Ranks(
                    $this->_alliance_query['alliance_ranks']
                );

                if ($_POST) {
                    // Save the data
                    $this->saveData($type);
                }
            }

        }

        $parse['al_sub_title'] = '';
        $parse['type']         = ($type != NULL)     ? $type     : 'info';
        $parse['alliance']     = ($alliance != NULL) ? $alliance : '';
        $parse['status']       = ($alliance != NULL) ? ''        : 'disabled';
        $parse['status_box']   = ($alliance != NULL) ? ''        : 'disabled';
        $parse['tag']          = ($alliance != NULL) ? 'a'       : 'button';
    
        $parse['content'] = 
            ($alliance != NULL && $type != NULL) ? $this->getData($type) : '';

        parent::$page->displayAdmin(
            $this->getTemplate()->set('adm/alliances_view', $parse)
        );
    }

    // -------------------------------------------------------- buildUsersCombo

    private function buildUsersCombo($user_id): string
    {
        $combo_rows = '';
        $users = $this->Alliances_Model->getAllUsers();

        foreach ($users as $users_row) {
            $combo_rows .= 
                '<option value="' 
                    . $users_row['user_id'] . '"' 
                    . $users_row['user_id'] == $user_id ? ' selected' : '' . '>' 
                    . $users_row['user_name'] . 
                '</option>';
        }

        return $combo_rows;
    }

    // ---------------------------------------------------------- checkAlliance

    private function checkAlliance($alliance): bool
    {
        if ($alliance_query = 
            $this->Alliances_Model->checkAllianceByNameOrTag($alliance)
        ) {
            $this->_id = $alliance_query['alliance_id'];

            return TRUE;
        }

        return FALSE;
    }

    // ---------------------------------------------------------------- getData

    private function getData($type)
    {
        switch ($type) {
            case 'info':
                break;

            case '':
                // No break

            case 'ranks':
                $this->getDataRanks();

            case 'members':
                return $this->getDataMembers();

            default:
                return $this->getDataInfo();
                break;
        }
    }

    // ------------------------------------------------------------ getDataInfo

    private function getDataInfo(): string
    {
        $parse  = $this->langs->language;
        $parse += (array) $this->_alliance_query;

        $ally_name = $this->_alliance_query['alliance_name'];
        $ally_info = $this->langs->line('al_alliance_information');

        $parse['al_alliance_information'] = str_replace('%s', 
                                                        $ally_name, 
                                                        $ally_info);

        $ally_reg_time = $this->_alliance_query['alliance_register_time'];
        $ally_owner    = $this->_alliance_query['alliance_owner'];

        $parse['alliance_register_time'] = $ally_reg_time == 0 ? '-' : date(
            FunctionsLib::readConfig('date_format_extended'), 
            $ally_reg_time
        );

        $parse['alliance_owner_picker']  = $this->buildUsersCombo($ally_owner);

        $ally_req_na = $this->_alliance_query['alliance_request_notallow'];

        $parse['sel1'] = $ally_req_na == 1 ? 'selected' : '';
        $parse['sel0'] = $ally_req_na == 0 ? 'selected' : '';

        $alert_type = $this->_alert_type;
        $alert_info = $this->_alert_info;

        $parse['alert_info'] = 
            $this->_alert_type != NULL ? Administration::saveMessage($alert_type, $alert_info) : '';

        return $this->getTemplate()->set(
            'adm/alliances_information_view', 
            $parse
        );
    }

    // --------------------------------------------------------- getDataMembers

    private function getDataMembers(): string
    {
        $parse = $this->langs->language;

        $ally_name         = $this->_alliance_query['alliance_name'];
        $ally_lang_members = $this->langs->line('al_alliance_members');

        $parse['al_alliance_members'] = str_replace('%s', 
                                                    $ally_name,
                                                    $ally_lang_members);

        $all_members = $this->Alliances_Model->getAllianceMembers($this->_id);

        $members = '';

        $al_req_no  = $this->langs->line('al_request_yes');
        $al_req_yes = $this->langs->line('al_request_no');
        $al_req_txt = $this->langs->line('ally_request_text');

        if (!empty($all_members)) {

            foreach ($all_members as $member) {
                $member['alliance_request']  = $member['user_ally_request']      ? $al_req_yes : $al_req_no;
                $member['ally_request_text'] = $member['user_ally_request_text'] ? $al_req_txt : '-';

                $member['alliance_register_time'] = date(
                    FunctionsLib::readConfig('date_format_extended'), 
                    $member['user_ally_register_time']
                );

                if ($member['user_id'] == $member['alliance_owner']) {
                    $member['ally_rank'] = $member['alliance_owner_range'];
                } else {

                    if (isset($member['user_ally_rank_id'])) {
                        $member['ally_rank'] = $this->ranks->getUserRankById(
                            $member['user_ally_rank_id']
                        )['rank'];
                    } else {
                        $al_rank_nd = $this->langs->line('al_rank_not_defined');
                        $member['ally_rank'] = $al_rank_nd;
                    }

                }

                $members .= $this->getTemplate()->set(
                    'adm/alliances_members_row_view', 
                    $member);
            }

        }

        $ally_lang_no_ranks = $this->langs->line('al_no_ranks');

        $parse['members_table'] = empty($members) ? '<tr><td colspan="6" class="align_center text-error">' . $ally_lang_no_ranks . '</td></tr>' : $members;
        $parse['alert_info']    = $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set(
            'adm/alliances_members_view', 
            $parse
        );
    }

    // ----------------------------------------------------------- getDataRanks

    private function getDataRanks(): string
    {
        $parse = $this->langs->language;

        $ally_name = $this->_alliance_query['alliance_name'];
        $ally_info = $this->langs->line('al_alliance_information');

        $parse['al_alliance_ranks'] = str_replace('%s', 
                                                  $ally_name, 
                                                  $ally_info);

        $parse['image_path'] = DEFAULT_SKINPATH;

        $alliance_ranks = $this->ranks->getAllRanksAsArray();
        $i = 0;

        $rank_row  = '';
        $rank_data = [];

        if (is_array($alliance_ranks)) {

            foreach ($alliance_ranks as $rank_id => $r_id) {
                $rank_data['name']                  = $r_id['rank'];
                $rank_data['delete']                = $r_id['rights'][AllianceRanks::delete]                 == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['kick']                  = $r_id['rights'][AllianceRanks::kick]                   == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['bewerbungen']           = $r_id['rights'][AllianceRanks::applications]           == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['memberlist']            = $r_id['rights'][AllianceRanks::view_member_list]       == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['administrieren']        = $r_id['rights'][AllianceRanks::administration]         == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['onlinestatus']          = $r_id['rights'][AllianceRanks::online_status]          == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['mails']                 = $r_id['rights'][AllianceRanks::send_circular]          == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['rechtehand']            = $r_id['rights'][AllianceRanks::right_hand]             == SwitchInt::on ? ' checked="checked"' : '';
                $rank_data['bewerbungenbearbeiten'] = $r_id['rights'][AllianceRanks::application_management] == SwitchInt::on ? ' checked="checked"' : '';

                $rank_data['i'] = $i++;

                $rank_row .= $this->getTemplate()->set(
                    'adm/alliances_ranks_row_view', 
                    $rank_data
                );
            }

        }

        $ally_no_ranks = $this->langs->line('al_no_ranks');

        $parse['ranks_table'] = empty($rank_row) ? $ally_no_ranks : $rank_row;
        $parse['alert_info']  = $this->_alert_type != NULL ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set(
            'adm/alliances_ranks_view', 
            $parse
        );
    }

    // --------------------------------------------------------------- saveData

    private function saveData($type): void
    {
        switch ($type) {
            case 'info':
                break;

            case '':
                // No break

            case 'ranks':
                $this->saveRanks();

            case 'members':
                return $this->saveMembers();
            
            default:
                // Save the data
                if (
                    isset($_POST['send_data']) 
                    && $_POST['send_data']
                ) {
                    $this->saveInfo();
                }

                break;
        }
    }

    // --------------------------------------------------------------- saveInfo

    private function saveInfo(): void
    {
        $ally_name      = $_POST['alliance_name'];
        $ally_name_orig = $_POST['alliance_name_orig'];

        $alliance_name      = isset($ally_name)     ? $ally_name     : '';
        $alliance_name_orig = isset($ally_tag_orig) ? $ally_tag_orig : '';

        $ally_tag      = $_POST['alliance_tag'];
        $ally_tag_orig = $_POST['alliance_tag_orig'];

        $alliance_tag      = isset($ally_tag)      ? $ally_tag      : '';
        $alliance_tag_orig = isset($ally_tag_orig) ? $ally_tag_orig : '';

        $ally_own       = $_POST['alliance_owner'];
        $ally_own_orig  = $_POST['alliance_owner_orig'];
        $ally_own_range = $_POST['alliance_owner_range'];

        $alliance_owner       = isset($ally_own)       ? $ally_own       : '';
        $alliance_owner_orig  = isset($ally_own_orig)  ? $ally_own_orig  : '';
        $alliance_owner_range = isset($ally_own_range) ? $ally_own_range : '';

        $ally_web  = $_POST['alliance_web'];
        $ally_img  = $_POST['alliance_image'];
        $ally_desc = $_POST['alliance_description'];
        $ally_txt  = $_POST['alliance_text'];

        $alliance_web         = isset($ally_web)  ? $ally_web : '';
        $alliance_image       = isset($ally_img)  ? $ally_img : '';
        $alliance_description = isset($ally_desc) ? $ally_desc : '';
        $alliance_text        = isset($ally_txt)  ? $ally_txt : '';

        $ally_req    = $_POST['alliance_request'];
        $ally_req_na = $_POST['alliance_request_notallow'];

        $alliance_request          = isset($ally_req)    ? $ally_req    : '';
        $alliance_request_notallow = isset($ally_req_na) ? $ally_req_na : '';

        $alliance_owner            = (int) $alliance_owner;
        $alliance_request_notallow = (int) $alliance_request_notallow;

        $errors = '';

        if ($alliance_name != $alliance_name_orig) {

            if (
                $alliance_name == NULL 
                || !$this->Alliances_Model->checkAllianceName($alliance_name)
            ) {
                $errors .= $this->langs->line('al_error_alliance_name') 
                        . '<br />';
            }

        }

        if ($alliance_tag != $alliance_tag_orig) {

            if (
                $alliance_tag == NULL 
                || !$this->Alliances_Model->checkAllianceTag($alliance_tag)
            ) {
                $errors .= $this->langs->line('al_error_alliance_tag') 
                        . '<br />';
            }

        }

        if ($alliance_owner != $alliance_owner_orig) {

            if (
                $alliance_owner <= 0 
                || $this->Alliances_Model->checkAllianceFounder($alliance_owner)
            ) {
                $errors .= $this->langs->line('al_error_founder') 
                        . '<br />';
            }

        }

        if ($errors != NULL) {
            $this->_alert_info = $errors;
            $this->_alert_type = 'warning';
        } else {
            $this->Alliances_Model->updateAllianceData([
                'alliance_name'             => $alliance_name,
                'alliance_tag'              => $alliance_tag,
                'alliance_owner'            => $alliance_owner,
                'alliance_owner_range'      => $alliance_owner_range,
                'alliance_web'              => $alliance_web,
                'alliance_image'            => $alliance_image,
                'alliance_description'      => $alliance_description,
                'alliance_text'             => $alliance_text,
                'alliance_request'          => $alliance_request,
                'alliance_request_notallow' => $alliance_request_notallow,
                'alliance_id'               => $this->_id,
            ]);

            $this->_alert_info = $this->langs->line('al_all_ok_message');
            $this->_alert_type = 'ok';
        }
    }

    // ------------------------------------------------------------ saveMembers

    private function saveMembers(): void
    {
        if (isset($_POST['delete_members'])) {
            $ids_string = '';

            if (isset($_POST['delete_message'])) {
                foreach ($_POST['delete_message'] as $user_id => $del_status) {

                    if (
                        $del_status == 'on' 
                        && $user_id > 0 
                        && is_numeric($user_id)
                    ) {
                        $ids_string .= $user_id . ',';
                    }

                }

                $amount = $this->Alliances_Model->countAllianceMembers(
                    $this->_id
                );

                if ($amount['Amount'] > 1) {
                    $this->Alliances_Model->removeAllianceMembers(
                        $ids_string
                    );

                    // Return the alert
                    $this->_alert_info = $this->langs->line(
                        'us_all_ok_message'
                    );

                    $this->_alert_type = 'ok';
                } else {
                    // Return the alert
                    $this->_alert_info = $this->langs->line(
                        'al_cant_delete_last_one'
                    );

                    $this->_alert_type = 'warning';
                }
            }
        }
    }

    // -------------------------------------------------------------- saveRanks

    private function saveRanks():void
    {
        if (isset($_POST['create_rank'])) {

            if (!empty($_POST['rank_name'])) {
                $this->ranks->addNew($_POST['rank_name']);

                $this->Alliances_Model->updateAllianceRanks(
                    $this->_id,
                    $this->ranks->getAllRanksAsJsonString()
                );

                $this->_alert_info = $this->langs->line('al_rank_added');
                $this->_alert_type = 'ok';
            } else {
                $this->_alert_info = $this->langs->line('al_required_name');
                $this->_alert_type = 'warning';
            }

        }

        // Edit rights for each rank
        if (isset($_POST['save_ranks'])) {

            foreach ($_POST['id'] as $id) {
                $this->ranks->editRankById(
                    $id,
                    [
                        AllianceRanks::delete                 => isset($_POST['u' . $id . 'r1']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::kick                   => isset($_POST['u' . $id . 'r2']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::applications           => isset($_POST['u' . $id . 'r3']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::view_member_list       => isset($_POST['u' . $id . 'r4']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::application_management => isset($_POST['u' . $id . 'r5']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::administration         => isset($_POST['u' . $id . 'r6']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::online_status          => isset($_POST['u' . $id . 'r7']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::send_circular          => isset($_POST['u' . $id . 'r8']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::right_hand             => isset($_POST['u' . $id . 'r9']) ? SwitchInt::on : SwitchInt::off,
                    ]
                );
            }

            $this->Alliances_Model->updateAllianceRanks(
                $this->_id,
                $this->ranks->getAllRanksAsJsonString()
            );

            $this->_alert_info = $this->langs->line('al_rank_saved');
            $this->_alert_type = 'ok';
        }

        // Delete a rank
        if (isset($_POST['delete_ranks'])) {

            foreach ($_POST['id'] as $rank_id) {
                $this->ranks->deleteRankById($rank_id);
            }

            $this->Alliances_Model->updateAllianceRanks(
                $this->_id,
                $this->ranks->getAllRanksAsJsonString()
            );

            $this->_alert_info = $this->langs->line('al_rank_removed');
            $this->_alert_type = 'ok';
        }

        FunctionsLib::redirect('admin.php?' . $_SERVER['QUERY_STRING']);
    }

}