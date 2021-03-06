<?php

/*
 * This file is part of the NextDom software (https://github.com/NextDom or http://nextdom.github.io).
 * Copyright (c) 2018 NextDom.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

$date = array(
    'start' => init('startDate', date('Y-m-d', strtotime('-1 month ' . date('Y-m-d')))),
    'end' => init('endDate', date('Y-m-d', strtotime('+1 days ' . date('Y-m-d')))),
);

if (init('object_id') == '') {
    $object = object::byId($_SESSION['user']->getOptions('defaultDashboardObject'));
} else {
    $object = object::byId(init('object_id'));
}
if (!is_object($object)) {
    $object = object::rootObject();
}
if (!is_object($object)) {
    throw new Exception('{{Aucun objet racine trouvé. Pour en créer un, allez dans Générale -> Objet.<br/> Si vous ne savez pas quoi faire ou que c\'est la premiere fois que vous utilisez Jeedom n\'hésitez pas a consulter cette <a href="http://jeedom.fr/premier_pas.php" target="_blank">page</a>}}');
}

sendVarToJs('eq_id', init('eq_id'));

?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <li class="nav-header"><i class="fa fa-bar-chart"></i> {{Surv. Equipement}}</li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                $allObject = object::buildTree();
                foreach ($allObject as $object) {
                    if ($object->getIsVisible() == 1 && count($object->getEqLogic(true, true, 'ethalsurveillance')) > 0) {
                        foreach ($object->getEqLogic() as $eqLogic) {
                            $margin = 5 ;
                            if ($eqLogic->getEqType_name() == 'ethalsurveillance' && $eqLogic->getIsEnable()) {
                                if ($eqLogic->getId() == init('eq_id')) {
                                    echo '<li class="cursor li_object active" ><a href="index.php?v=d&m=ethalsurveillance&p=panel&eq_id=' . $eqLogic->getId() . '" style="position:relative;left:5 px;">' . $eqLogic->getHumanName(true) . '</a></li>';
                                }else{
                                    echo '<li class="cursor li_object" ><a href="index.php?v=d&m=ethalsurveillance&p=panel&eq_id=' . $eqLogic->getId() . '" style="position:relative;left:5 px;">' . $eqLogic->getHumanName(true) . '</a></li>';
                                }
                            }
                        }
                    }
                }
                ?>               
           </ul>
       </div>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-8">
        <div class="row">
            <legend style="height: 40px;">
                <span>{{Information de Surveillance}}</span>
                <span>
                    <form class="form-inline pull-right">
                        <div class = "form-group">
                            <label for = "in_startDate" style="font-weight: normal;">{{Du}}</label>
                            <input id="in_startDate" class="form-control input-sm in_datepicker" style="width: 150px;" value="<?php echo $date['start'] ?>"/>
                        </div>
                        <div class = "form-group">
                            <label for = "in_endDate" style="font-weight: normal;">{{Au}}</label> 
                            <input id="in_endDate" class="form-control input-sm in_datepicker" style="width: 150px;" value="<?php echo $date['end'] ?>"/>
                        </div>
                        <div class = "form-group">
                            <a class="btn btn-success btn-sm" id='bt_validChangeDate'>{{Ok}}</a>
                        </div>
                        <div class = "form-group">
                            <select class="form-control" id="sel_groupingType" style="width: 200px;">
                                <option value="cumulday">{{Cumul par jour}}</option>
                                <option value="cumulweek">{{Cumul par semaine}}</option>
                                <option value="cumulmonth">{{Cumul par mois}}</option>
                            </select>
                        </div>
                    </form>
                </span> 
            </legend>
        </div>
        <div class="row">
            <div class="col-lg-3" id="div_displayEquipement"></div>
            <div class="col-lg-9" id="div_graphic_tpsfct"></div>
        </div>
        <div class="row">
            <legend>{{Equipement surveillé}}</legend>
            <div class="col-lg-12" id="div_displayEquipementMaster"></div>
        </div>
    </div>

</div>

<?php include_file('desktop', 'cumul', 'js', 'ethalsurveillance');?>
