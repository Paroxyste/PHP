<?php

declare (strict_types = 1);

namespace application\controllers\adm;

use application\core\Controller;

use application\libraries\FleetsLib;
use application\libraries\FormatLib as Format;
use application\libraries\TimingLibrary as Timing;

use application\libraries\adm\AdministrationLib as Administration;

class Fleets extends Controller
{
    private array $user;

    /* SUMMARY
     * 
     * constructor
     * buildActionsBlock
     * buildAmountBlock
     * buildArrivalBlock
     * buildBeginningBlock
     * buildDepartureBlock
     * buildFleetMovementsBlock
     * buildMissionBlock
     * buildObjectiveBlock
     * buildPage
     * buildReturnBlock
     * doDeleteAction
     * doEndAction
     * doRestartAction
     * doReturnAction
     * runAction
     * 
     */

    // ------------------------------------------------------------ constructor

    public function __construct()
    {
        parent::__construct();

        // Check if session is active
        Administration::checkSession();

        // Load model + language
        parent::loadModel('adm/fleets');
        parent::loadLang(['adm/global', 'adm/objects', 'adm/fleets']);

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

    // ------------------------------------------------------ buildActionsBlock

    private function buildActionsBlock(array $fleet): array
    {
        return [
            'fleet_id' => $fleet['fleet_id']
        ];
    }

    // ------------------------------------------------------- buildAmountBlock

    private function buildAmountBlock(array $fleet): array
    {
        $pop_up = [];

        foreach (FleetsLib::getFleetShipsArray($fleet['fleet_array']) as $ship => $amount) {
            $pop_up[] = 
                $this->langs->language['objects'][$ship] 
                . ': ' . 
                Format::prettyNumber($amount);
        }

        return [
            'amount'         => $this->langs->line('ff_ships'),
            'amount_content' => join('<br>', $pop_up),
        ];
    }

    // ------------------------------------------------------ buildArrivalBlock

    private function buildArrivalBlock(array $fleet): array
    {
        return [
            'arrival' => Timing::formatExtendedDate($fleet['fleet_start_time'])
        ];
    }

    // ---------------------------------------------------- buildBeginningBlock

    private function buildBeginningBlock(array $fleet): array
    {
        return [
            'beginning' => Format::prettyCoords(
                $fleet['fleet_start_galaxy'],
                $fleet['fleet_start_system'],
                $fleet['fleet_start_planet']
            ),
        ];
    }

    // ---------------------------------------------------- buildDepartureBlock

    private function buildDepartureBlock(array $fleet): array
    {
        return [
            'departure' => Timing::formatExtendedDate($fleet['fleet_creation'])
        ];
    }

    // ----------------------------------------------- buildFleetMovementsBlock

    private function buildFleetMovementsBlock(): array
    {
        $fleets = $this->Fleets_Model->getAllFleets();
        $fleet_movements = [];

        foreach ($fleets as $fleet) {
            $fleet_movements[] = array_merge(
                $this->langs->language,
                $this->buildMissionBlock($fleet),
                $this->buildAmountBlock($fleet),
                $this->buildBeginningBlock($fleet),
                $this->buildDepartureBlock($fleet),
                $this->buildObjectiveBlock($fleet),
                $this->buildArrivalBlock($fleet),
                $this->buildReturnBlock($fleet),
                $this->buildActionsBlock($fleet)
            );
        }

        return [
            'fleet_movements' => $fleet_movements
        ];
    }

    // ------------------------------------------------------ buildMissionBlock

    private function buildMissionBlock(array $fleet): array
    {
        $ff_r = $this->langs->line('ff_r');
        $ff_a = $this->langs->line('ff_a');
        
    
        return [
            'mission' => 
                $this->langs->language['ff_type_mission'][$fleet['fleet_mission']] 
                . ' ' . 
                FleetsLib::isFleetReturning($fleet['fleet_mess']) ? $ff_r : $ff_a,

            'metal' => Format::prettyNumber(
                $fleet['fleet_resource_metal']
            ),

            'crystal' => Format::prettyNumber(
                $fleet['fleet_resource_crystal']
            ),

            'deuterium' => Format::prettyNumber(
                $fleet['fleet_resource_deuterium']
            ),
        ];
    }

    // ---------------------------------------------------- buildObjectiveBlock

    private function buildObjectiveBlock(array $fleet): array
    {
        return [
            'objective' => Format::prettyCoords(
                $fleet['fleet_end_galaxy'],
                $fleet['fleet_end_system'],
                $fleet['fleet_end_planet']
            ),
        ];
    }

    // -------------------------------------------------------------- buildPage

    private function buildPage(): void
    {
        parent::$page->displayAdmin($this->getTemplate()->set(
            'adm/fleets_view',
            array_merge(
                $this->langs->language, 
                $this->buildFleetMovementsBlock()
            )
        ));
    }

    // ------------------------------------------------------- buildReturnBlock

    private function buildReturnBlock(array $fleet): array
    {
        return [
            'return' => Timing::formatExtendedDate($fleet['fleet_end_time'])
        ];
    }

    // --------------------------------------------------------- doDeleteAction

    private function doDeleteAction(int $fleet_id): void
    {
        $this->Fleets_Model->deleteFleetById($fleet_id);
    }

    // ------------------------------------------------------------ doEndAction

    private function doEndAction(int $fleet_id): void
    {
        $this->Fleets_Model->endFleetById($fleet_id);
    }

    // -------------------------------------------------------- doRestartAction

    private function doRestartAction(int $fleet_id): void
    {
        $this->Fleets_Model->restartFleetById($fleet_id);
    }

    // --------------------------------------------------------- doReturnAction

    private function doReturnAction(int $fleet_id): void
    {
        $this->Fleets_Model->returnFleetById($fleet_id);
    }

    // -------------------------------------------------------------- runAction

    private function runAction(): void
    {
        $action   = filter_input(INPUT_GET, 'action');
        $fleet_id = filter_input(INPUT_GET, 'fleetId', FILTER_VALIDATE_INT);

        if (
            in_array($action, ['restart', 'end', 'return', 'delete']) 
            && $fleet_id
        ) {
            $this->{'do' . ucfirst($action) . 'Action'}($fleet_id);
        }
    }

}
