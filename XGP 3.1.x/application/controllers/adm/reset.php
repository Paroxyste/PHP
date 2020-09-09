<?php

namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;


class Reset extends Controller
{
    private string $alert;
    private array  $user;

    /* SUMMARY
     *
     * constructor
     * buildPage
     * runAction
     * 
     */

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/reset');
        parent::loadLang(['adm/global', 'adm/reset']);

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

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/reset_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert' => $this->alert ? $this->alert : '',
                    ]
                )
            )
        );
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        if ($_POST) {

            if (!isset($_POST['resetall'])) {
                // Reset defenses
                if (
                    isset($_POST['defenses']) 
                    && $_POST['defenses'] == 'on'
                ) {
                    $this->Reset_Model->resetDefenses();
                }

                // Reset ships
                if (
                    isset($_POST['ships']) 
                    && $_POST['ships'] == 'on'
                ) {
                    $this->Reset_Model->resetShips();
                }

                // Reset shipyard queues
                if (
                    isset($_POST['h_d']) && 
                    $_POST['h_d'] == 'on'
                ) {
                    $this->Reset_Model->resetShipyardQueues();
                }

                // Reset planet buildings
                if (
                    isset($_POST['edif_p']) 
                    && $_POST['edif_p'] == 'on'
                ) {
                    $this->Reset_Model->resetPlanetBuildings();
                }

                // Reset moon buildings
                if (
                    isset($_POST['edif_l']) 
                    && $_POST['edif_l'] == 'on'
                ) {
                    $this->Reset_Model->resetMoonBuildings();
                }

                // Reset buildings queues
                if (
                    isset($_POST['edif']) 
                    && $_POST['edif'] == 'on'
                ) {
                    $this->Reset_Model->resetBuildingsQueues();
                }

                // Reset research
                if (
                    isset($_POST['inves']) 
                    && $_POST['inves'] == 'on'
                ) {
                    $this->Reset_Model->resetResearch();
                }

                // Reset research queues
                if (
                    isset($_POST['inves_c']) 
                    && $_POST['inves_c'] == 'on'
                ) {
                    $this->Reset_Model->resetResearchQueues();
                }

                // Reset officiers
                if (
                    isset($_POST['ofis']) 
                    && $_POST['ofis'] == 'on'
                ) {
                    $this->Reset_Model->resetOfficiers();
                }

                // Reset dark matter
                if (
                    isset($_POST['dark']) 
                    && $_POST['dark'] == 'on'
                ) {
                    $this->Reset_Model->resetDarkMatter();
                }

                // Reset resources
                if (
                    isset($_POST['resources']) 
                    && $_POST['resources'] == 'on'
                ) {
                    $this->Reset_Model->resetResources();
                }

                // Reset notes
                if (
                    isset($_POST['notes']) 
                    && $_POST['notes'] == 'on'
                ) {
                    $this->Reset_Model->resetNotes();
                }

                // Reset reports
                if (
                    isset($_POST['rw']) 
                    && $_POST['rw'] == 'on'
                ) {
                    $this->Reset_Model->resetReports();
                }

                // Reset friends
                if (
                    isset($_POST['friends']) 
                    && $_POST['friends'] == 'on'
                ) {
                    $this->Reset_Model->resetFriends();
                }

                // Reset alliances
                if (
                    isset($_POST['alliances']) 
                    && $_POST['alliances'] == 'on'
                ) {
                    $this->Reset_Model->resetAlliances();
                }

                // Reset fleets
                if (
                    isset($_POST['fleets']) 
                    && $_POST['fleets'] == 'on'
                ) {
                    $this->Reset_Model->resetFleets();
                }

                // Reset banned
                if (
                    isset($_POST['banneds']) 
                    && $_POST['banneds'] == 'on'
                ) {
                    $this->Reset_Model->resetBanned();
                }

                // Reset messages
                if (
                    isset($_POST['messages']) 
                    && $_POST['messages'] == 'on'
                ) {
                    $this->Reset_Model->resetMessages();
                }

                // Reset statistics
                if (
                    isset($_POST['statpoints']) 
                    && $_POST['statpoints'] == 'on'
                ) {
                    $this->Reset_Model->resetStatistics();
                }

                // Reset moons
                if (
                    isset($_POST['moons']) 
                    && $_POST['moons'] == 'on'
                ) {
                    $this->Reset_Model->resetMoons();
                }

            } else {
                // Reset everything
                $this->Reset_Model->resetAll();
            }

            $this->alert = Administration::saveMessage(
                'ok', 
                $this->langs->line('re_reset_excess')
            );

        }
    }

}