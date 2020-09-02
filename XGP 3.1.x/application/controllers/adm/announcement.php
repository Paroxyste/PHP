<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\MessagesEnumerator;
use application\core\enumerators\UserRanksEnumerator as UserRanks;

use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\adm\AdministrationLib as Administration;

use JS_PATH;

class Announcement extends Controller
{

    private array $alerts;
    private array $user;

    /* SUMMARY
     *
     * constructor
     * buildColorPicker
     * buildPage
     * doEmailAction
     * doMessageAction
     * getMessageColor
     * isValidColor
     * runAction
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        //Check if session is active
        Administration::checkSession();

        // Load Model
        parent::loadModel('adm/announcement');

        // Load Language
        parent::loadLang(['adm/global', 'adm/announcement']);

        // Set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (
            !Administration::authorization(
                __CLASS__, 
                (int) $this->user['user_authlevel'])
        ) {
            die(Administration::noAccessMessage(
                $this->langs->line('no_permissions')
            ));
        }

        // Time to do something
        $this->runAction();

        // Build the page
        $this->buildPage();
    }

    // ------------------------------------------------------- buildColorPicker

    private function buildColorPicker(): array
    {
        $colors_list = [];

        foreach (Format::getHTMLColorsNameList() as $color) {
            $colors_list[] = ['color' => $color,];
        }

        return ['colors' => $colors_list,];
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/announcement_view',
                array_merge($this->langs->language, $this->buildColorPicker(),
                    [
                        'js_path' => JS_PATH,
                        'alert' => $this->alerts ? join('', $this->alerts) : '',
                    ]
                )
            )
        );
    }

    // ---------------------------------------------------------- doEmailAction

    private function doEmailAction(array $post): void
    {
        $players = $this->Announcement_Model->getAllPlayers();
        $from    = [
            'mail' => Functions::readConfig('admin_email'),
            'name' => Functions::readConfig('game_name'),
        ];

        $sent_count = 0;
        $results    = [];

        foreach ($players as $player) {
            $p_user = $player['user_name'];
            $p_mail = $player['user_email'];
            $post   = $post['subject'];

            $an_none = $this->langs->line('an_none');
            $an_sent = $this->langs->line('an_email_sent');
            $an_fail = $this->langs->line('an_email_failed');

            $result = Functions::sendEmail(
                $p_mail,
                $post ?? $an_none,
                strtr($post['text'], 
                      ['%player%' => Format::strongText($p_user)]
                ),
                $from
            );

            $results[] = $p_user . ': ' . ($result ? $an_sent : $an_fail);

            // 20 per row
            if (
                $sent_count % 20 == 0
            ) {
                // Wait, prevent flooding
                sleep(1); 
            }

            $sent_count++;
        }

        $this->alerts[] = Administration::saveMessage(
            'info',
            strtr($this->langs->line('an_delivery_result'), 
                [
                    '%s' => join('<br>', $results)
                ]
            )
        );
    }

    // -------------------------------------------------------- doMessageAction

    private function doMessageAction(array $post): void
    {
        $players = $this->Announcement_Model->getAllPlayers();

        if (
            isset($post['color-picker'])
        ) {
            $color = $post['color-picker'];
        } else {
            $color = $this->getMessageColor()[$this->user['user_authlevel']];
        }

        $level = $this->langs->language['user_level'][$this->user['user_authlevel']];
        $time = time();

        $an_none = $this->langs->line('an_none');

        $from    = Format::customColor($level, $color);
        $subject = Format::customColor($post['subject'] ?? $an_none, $color);
        $message = Format::customColor($post['text'], $color);

        foreach ($players as $player) {
            Functions::sendMessage(
                $player['user_id'],
                $this->user['user_id'],
                $time,
                MessagesEnumerator::GENERAL,
                $from,
                $subject,
                strtr($message, 
                    [
                        '%player%' => Format::strongText($player['user_name'])
                    ]
                ),
                TRUE
            );
        }

        $an_sent = $this->langs->line('an_sent');
        $this->alerts[] = Administration::saveMessage('ok', $an_sent);
    }

    // -------------------------------------------------------- getMessageColor

    private function getMessageColor(): array
    {
        return [
            UserRanks::GO    => 'yellow',
            UserRanks::SGO   => 'skyblue',
            UserRanks::ADMIN => 'red',
        ];
    }

    // ----------------------------------------------------------- isValidColor

    private function isValidColor(string $color): string
    {
        if (
            in_array($color, Format::getHTMLColorsNameList())
        ) {
            return $color;
        }

        return '';
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $action = filter_input_array(
            INPUT_POST,
            [
                'subject' => FILTER_SANITIZE_STRING,
                'message' => FILTER_SANITIZE_STRING,
                'mail'    => FILTER_SANITIZE_STRING,

                'color-picker' => [
                    'filter'  => FILTER_CALLBACK,
                    'options' => [$this, 'isValidColor'],
                ],

                'text' => [
                    'filter'  => FILTER_SANITIZE_STRING,
                    'options' => ['min_range' => 1, 'max_range' => 5000],
                ],
            ],
            FALSE
        );

        if (
            $action
        ) {
            if (
                isset($action['text']) 
                && $action['text'] != ''
            ) {

                if (
                    isset($action['message'])
                ) {
                    $this->doMessageAction($action);
                }

                if (
                    isset($action['mail'])
                ) {
                    $this->doEmailAction($action);
                }
            } else {
                $this->alerts[] = Administration::saveMessage(
                    'warning', 
                    $this->langs->line('an_not_sent')
                );
            }

        }
    }

}