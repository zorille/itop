<?php
// Copyright (C) 2014 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

use salesforce_data_collector\collectors\ConnexionManager;
use Zorille\salesforce\query_builder;

require_once APPROOT . 'collectors/ConnexionManager.class.php';

$connexion = new ConnexionManager($argv);
$connexion->LoadData();
query_builder::setConnexion($connexion);

$liste_option = $connexion->getListOptions() ?? null;
global $liste_option;

require_once APPROOT .'/collectors/includes.php';

Orchestrator::AddCollector(1, SFDCOrganizationsCollector::class);
Orchestrator::AddCollector(2, SFDCLocationsCollector::class);
Orchestrator::AddCollector(3, SFDCContactsPersonsCollector::class);
Orchestrator::AddCollector(4, SFDCContactsTeamsCollector::class);
//Orchestrator::AddCollector(3, SFDCOpportunitiesCollector::class);
Orchestrator::AddCollector(5, SFDCAssetsCollector::class);
Orchestrator::AddCollector(6, SFDCProductFamiliesCollector::class);
Orchestrator::AddCollector(6, SFDCProductsCollector::class);
Orchestrator::AddCollector(7, SFDCContractsCollector::class);
