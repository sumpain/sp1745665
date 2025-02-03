<?php

function lsd_gform_after_submission( $entry, $form ) {
	/* Store number of openers, transoms and cruciforms for each window type, used to calculate the final price */
	$window_prices = array(
		array([0,0,0],[0,1,0],[0,1,0],[0,2,1],[0,2,0],[0,4,2],[0,3,0],[0,5,3]),
		array([1,0,0],[1,0,0],[1,0,0],[1,0,0],[1,1,0],[1,1,0],[1,2,0],[1,2,0],[1,2,0],[1,2,0],[1,1,0],[1,2,1],[1,2,1],[1,4,2],[1,4,2]),
		array([2,0,0],[2,0,0],[2,0,0],[2,0,0],[2,1,2],[2,0,0],[2,1,2],[2,2,2],[2,0,0],[2,2,0],[2,1,0],[2,2,0],[2,2,2],[2,2,2],[2,1,0],[2,3,0],[2,3,0],[2,5,3],[2,5,3],[2,1,0]),
		array([3,0,0],[3,0,0],[3,1,0],[3,4,2],[3,4,2],[3,0,0],[3,0,2]),
		array([4,0,0],[4,1,2],[4,0,0],[4,2,3],[4,1,0],[4,2,3],[4,0,0],[4,2,3]),
	);

	/* Get base and fitted prices of Composite Doors, used as base for the final price */
	$compositedoors_baseprices = array();
	$compositedoors_fittedprices = array();
	$option = get_option('lsdsettings_compositedoors');
	for($i=1; $i<21; $i++) {
		array_push($compositedoors_baseprices, $option['lsdcompositedoors-formulas-baseprice']['style_'.$i]);
		array_push($compositedoors_fittedprices, $option['lsdcompositedoors-formulas-basepricefitted']['style_'.$i]);
	}
	$composite_prices = array(
		$compositedoors_baseprices,
		$compositedoors_fittedprices,
	);

	/* Get base, fitted and finish prices of Profile Doors, used to calculate final price */
	$profiledoors_baseprices = array();
	$profiledoors_wgowprices = array();
	$profiledoors_wgbsprices = array();
	$profiledoors_prowprices = array();
	$profiledoors_prbsprices = array();
	$profiledoors_fittedprices = array();
	$profiledoors_fwgowprices = array();
	$profiledoors_fwgbsprices = array();
	$profiledoors_fprowprices = array();
	$profiledoors_fprbsprices = array();
	$option = get_option('lsdsettings_profiledoors');
	for($i=1; $i<17; $i++) {
		array_push($profiledoors_baseprices, $option['lsdprofiledoors-type'.$i]['baseprice']);
		array_push($profiledoors_wgowprices, $option['lsdprofiledoors-type'.$i]['wgow']);
		array_push($profiledoors_wgbsprices, $option['lsdprofiledoors-type'.$i]['wgbs']);
		array_push($profiledoors_prowprices, $option['lsdprofiledoors-type'.$i]['prow']);
		array_push($profiledoors_prbsprices, $option['lsdprofiledoors-type'.$i]['prbs']);
		array_push($profiledoors_fittedprices, $option['lsdprofiledoors-type'.$i]['fitted']);
		array_push($profiledoors_fwgowprices, $option['lsdprofiledoors-type'.$i]['fwgow']);
		array_push($profiledoors_fwgbsprices, $option['lsdprofiledoors-type'.$i]['fwgbs']);
		array_push($profiledoors_fprowprices, $option['lsdprofiledoors-type'.$i]['fprow']);
		array_push($profiledoors_fprbsprices, $option['lsdprofiledoors-type'.$i]['fprbs']);
	}
	$profile_prices = array(
		$profiledoors_baseprices,
		$profiledoors_wgowprices,
		$profiledoors_wgbsprices,
		$profiledoors_prowprices,
		$profiledoors_prbsprices,
		$profiledoors_fittedprices,
		$profiledoors_fwgowprices,
		$profiledoors_fwgbsprices,
		$profiledoors_fprowprices,
		$profiledoors_fprbsprices,
	);

	/* Calculate final prices for each of the available options */
	if ($form["id"]===1) {
		$width = $entry[15];
		$height = $entry[16];
		$email = $entry[20];
		$framework = $entry[2];
		// 1 = windows; 2 = bifolding doors; 3 = french doors; 4 = patio doors; 5 = profile doors; 6 = panelled doors; 7 = composite doors;
		switch($framework) {
			case '1':
				// UPC Windows
				$option = get_option('lsdsettings');
				$k1 = $option['lsdwindows-formulas-baseprice']['factor_1']+0;
				$k2 = $option['lsdwindows-formulas-baseprice']['factor_2']+0;
				$fk1 = $option['lsdwindows-formulas-basepricefitted']['factor_1']+0;
				$fk2 = $option['lsdwindows-formulas-basepricefitted']['factor_2']+0;
				$fk3 = $option['lsdwindows-formulas-basepricefitted']['factor_3']+0;
				$m1 = $option['lsdwindows-multipliers-wgow']['multiplier']+0;
				$m2 = $option['lsdwindows-multipliers-wgbs']['multiplier']+0;
				$m3 = $option['lsdwindows-multipliers-prow']['multiplier']+0;
				$m4 = $option['lsdwindows-multipliers-prbs']['multiplier']+0;
				$fm1 = $option['lsdwindows-multipliers-wgow']['multiplier-fitted']+0;
				$fm2 = $option['lsdwindows-multipliers-wgbs']['multiplier-fitted']+0;
				$fm3 = $option['lsdwindows-multipliers-prow']['multiplier-fitted']+0;
				$fm4 = $option['lsdwindows-multipliers-prbs']['multiplier-fitted']+0;
				$o1 = $option['lsdwindows-openers-white']['fee']+0;
				$o2 = $option['lsdwindows-openers-wgow']['fee']+0;
				$o3 = $option['lsdwindows-openers-wgbs']['fee']+0;
				$o4 = $option['lsdwindows-openers-prow']['fee']+0;
				$o5 = $option['lsdwindows-openers-prbs']['fee']+0;
				$t1 = $option['lsdwindows-transoms-white']['fee']+0;
				$t2 = $option['lsdwindows-transoms-wgow']['fee']+0;
				$t3 = $option['lsdwindows-transoms-wgbs']['fee']+0;
				$t4 = $option['lsdwindows-transoms-prow']['fee']+0;
				$t5 = $option['lsdwindows-transoms-prbs']['fee']+0;
				$c1 = $option['lsdwindows-cruciforms-all']['fee']+0;

				$openers = intval($entry[3]);
				$layout = intval($entry[9+$openers]);
				$transoms = $window_prices[$openers][$layout-1][1];
				$cruciforms = $window_prices[$openers][$layout-1][2];
				$baseprice = (($width*$height/$k1))*$k2;
				$basepricefitted = (($width*$height/$fk1)+$fk2)*$fk3;
				$cost = $baseprice + $o1*$openers + $t1*$transoms + $c1*$cruciforms;
				$wgow = $baseprice*$m1 + $o2*$openers + $t2*$transoms + $c1*$cruciforms;
				$wgbs = $baseprice*$m2 + $o3*$openers + $t3*$transoms + $c1*$cruciforms;
				$prow = $baseprice*$m3 + $o4*$openers + $t4*$transoms + $c1*$cruciforms;
				$prbs = $baseprice*$m4 + $o5*$openers + $t5*$transoms + $c1*$cruciforms;
				$fitted = $basepricefitted-$baseprice;
				$fwgow = ($wgow - $baseprice*$m1 + $basepricefitted*$fm1)-$wgow;
				$fwgbs = ($wgbs - $baseprice*$m2 + $basepricefitted*$fm2)-$wgbs;
				$fprow = ($prow - $baseprice*$m3 + $basepricefitted*$fm3)-$prow;
				$fprbs = ($prbs - $baseprice*$m4 + $basepricefitted*$fm4)-$prbs;
				break;
			case '2':
				// Bi-folding doors
				$option = get_option('lsdsettings_bifoldingdoors');
				$k1 = $option['lsdbifoldingdoors-formulas-baseprice']['price_per_door']+0;
				$k2 = $option['lsdbifoldingdoors-formulas-baseprice']['width_factor']+0;
				$k3 = $option['lsdbifoldingdoors-formulas-baseprice']['height_factor']+0;
				$k4 = $option['lsdbifoldingdoors-formulas-baseprice']['quantity_factor']+0;
				$k5 = $option['lsdbifoldingdoors-formulas-baseprice']['multiplier']+0;
				$f1 = $option['lsdbifoldingdoors-formulas-basepricefitted']['multiplier']+0;
				$m1 = $option['lsdbifoldingdoors-multipliers-wgow']['multiplier']+0;
				$m2 = $option['lsdbifoldingdoors-multipliers-wgbs']['multiplier']+0;
				$m3 = $option['lsdbifoldingdoors-multipliers-prow']['multiplier']+0;
				$m4 = $option['lsdbifoldingdoors-multipliers-prbs']['multiplier']+0;
				$fm1 = $option['lsdbifoldingdoors-multipliers-wgow']['multiplier-fitted']+0;
				$fm2 = $option['lsdbifoldingdoors-multipliers-wgbs']['multiplier-fitted']+0;
				$fm3 = $option['lsdbifoldingdoors-multipliers-prow']['multiplier-fitted']+0;
				$fm4 = $option['lsdbifoldingdoors-multipliers-prbs']['multiplier-fitted']+0;

				$doors = intval($entry[4]);
				$cost = (($k1*$doors)+((($width/$k2)*($height/$k3))-($k4*$doors))*$k5);
				$wgow = $cost*$m1;
				$wgbs = $cost*$m2;
				$prow = $cost*$m3;
				$prbs = $cost*$m4;
				$fitted = $doors*$f1;
				$fwgow = $fitted*$fm1;
				$fwgbs = $fitted*$fm2;
				$fprow = $fitted*$fm3;
				$fprbs = $fitted*$fm4;
				break;
			case '3':
				// French doors
				$option = get_option('lsdsettings_frenchdoors');
				$frenchdoors_baseprices = array();
				for($i=1; $i<6; $i++) {
					array_push($frenchdoors_baseprices,$option['lsdfrenchdoors-prices-doorstyle']['style_'.$i]+0);
				}
				$k1 = $option['lsdfrenchdoors-formulas-baseprice']['width_factor']+0;
				$k2 = $option['lsdfrenchdoors-formulas-baseprice']['multiplier_1']+0;
				$k3 = $option['lsdfrenchdoors-formulas-baseprice']['multiplier_2']+0;
				$f1 = $option['lsdfrenchdoors-formulas-basepricefitted']+0;
				$m1 = $option['lsdfrenchdoors-multipliers-wgow']['multiplier']+0;
				$m2 = $option['lsdfrenchdoors-multipliers-wgbs']['multiplier']+0;
				$m3 = $option['lsdfrenchdoors-multipliers-prow']['multiplier']+0;
				$m4 = $option['lsdfrenchdoors-multipliers-prbs']['multiplier']+0;
				$fm1 = $option['lsdfrenchdoors-multipliers-wgow']['multiplier-fitted']+0;
				$fm2 = $option['lsdfrenchdoors-multipliers-wgbs']['multiplier-fitted']+0;
				$fm3 = $option['lsdfrenchdoors-multipliers-prow']['multiplier-fitted']+0;
				$fm4 = $option['lsdfrenchdoors-multipliers-prbs']['multiplier-fitted']+0;

				$style = intval($entry[5]);
				$stylefee = $frenchdoors_baseprices;
				$cost = (($stylefee[$style-1]+(($width-$k1)*$k2))*$k3)-$f1;
				$wgow = $cost*$m1;
				$wgbs = $cost*$m2;
				$prow = $cost*$m3;
				$prbs = $cost*$m4;
				$fitted = $f1;
				$fwgow = $fitted*$fm1;
				$fwgbs = $fitted*$fm2;
				$fprow = $fitted*$fm3;
				$fprbs = $fitted*$fm4;
				break;
			case '4':
				// Patio doors
				$option = get_option('lsdsettings_patiodoors');
				$frenchdoors_baseprices = array();
				$frenchdoors_fittingprices = array();
				for($i=1; $i<4; $i++) {
					array_push($frenchdoors_baseprices,$option['lsdpatiodoors-prices-doorstyle']['style_'.$i]+0);
					array_push($frenchdoors_fittingprices,$option['lsdpatiodoors-prices-doorstyle']['fitting_'.$i]+0);
				}
				$k1 = $option['lsdpatiodoors-formulas-baseprice']['height_factor']+0;
				$k2 = $option['lsdpatiodoors-formulas-baseprice']['multiplier_1']+0;
				$k3 = $option['lsdpatiodoors-formulas-baseprice']['multiplier_2']+0;
				$m1 = $option['lsdpatiodoors-multipliers-wgow']['multiplier']+0;
				$m2 = $option['lsdpatiodoors-multipliers-wgbs']['multiplier']+0;
				$m3 = $option['lsdpatiodoors-multipliers-prow']['multiplier']+0;
				$m4 = $option['lsdpatiodoors-multipliers-prbs']['multiplier']+0;
				$fm1 = $option['lsdpatiodoors-multipliers-wgow']['multiplier-fitted']+0;
				$fm2 = $option['lsdpatiodoors-multipliers-wgbs']['multiplier-fitted']+0;
				$fm3 = $option['lsdpatiodoors-multipliers-prow']['multiplier-fitted']+0;
				$fm4 = $option['lsdpatiodoors-multipliers-prbs']['multiplier-fitted']+0;

				$panes = intval($entry[6])-2;
				$panesfee = $frenchdoors_baseprices;
				$fittingfee = $frenchdoors_fittingprices;
				$cost = (($panesfee[$panes]+(($width-$k1)*$k2))*$k3)-$fittingfee[$panes];
				$wgow = $cost*$m1;
				$wgbs = $cost*$m2;
				$prow = $cost*$m3;
				$prbs = $cost*$m4;
				$fitted = $fittingfee[$panes];
				$fwgow = $fitted*$fm1;
				$fwgbs = $fitted*$fm2;
				$fprow = $fitted*$fm3;
				$fprbs = $fitted*$fm4;
				break;
			case '5':
				// Profile doors
				$style = intval($entry[22]);
				$cost = $profile_prices[0][$style-1];
				$wgow = $profile_prices[1][$style-1];
				$wgbs = $profile_prices[2][$style-1];
				$prow = $profile_prices[3][$style-1];
				$prbs = $profile_prices[4][$style-1];
				$fitted = $profile_prices[5][$style-1] - $cost;
				$fwgow = $profile_prices[6][$style-1] - $wgow;
				$fwgbs = $profile_prices[7][$style-1] - $wgbs;
				$fprow = $profile_prices[8][$style-1] - $prow;
				$fprbs = $profile_prices[9][$style-1] - $prbs;
				break;
			case '6':
				// Panelled doors
				$option = get_option('lsdsettings_panelleddoors');
				$basefee = $option['lsdpanelleddoors-base']+0;
				$wgowfee = $option['lsdpanelleddoors-wgow']+0;
				$wgbsfee = $option['lsdpanelleddoors-wgbs']+0;
				$prowfee = $option['lsdpanelleddoors-prow']+0;
				$prbsfee = $option['lsdpanelleddoors-prbs']+0;
				$fittedfee = $option['lsdpanelleddoors-fitted']+0;
				$fwgowfee = $option['lsdpanelleddoors-fwgow']+0;
				$fwgbsfee = $option['lsdpanelleddoors-fwgbs']+0;
				$fprowfee = $option['lsdpanelleddoors-fprow']+0;
				$fprbsfee = $option['lsdpanelleddoors-fprbs']+0;
				$glazingoptions_per_style = array(11, 4, 4, 7, 6, 10, 6, 5, 7, 3, 4, 4, 5, 7, 3, 3, 4, 3, 11);
				$glazingfees = array();
				for($i=0; $i<count($glazingoptions_per_style); $i++) {
					$tmp = array();
					for($j=0; $j<$glazingoptions_per_style[$i]; $j++) {
						array_push($tmp,$option['lsdpanelleddoors-glazingoptions-'.($i+1).'-price']['option_'.($j+1)]);
					}
					array_push($glazingfees,$tmp);
				}

				$style = intval($entry[23]);
				if ($style<7) { $glazingopt = intval($entry[25+$style]); }
				if ($style==7) { $glazingopt = intval($entry[65]); }
				if ($style>7) { $glazingopt = intval($entry[24+$style]); }
				$gfee = $glazingfees[$style-1][$glazingopt-1];
				$cost = $basefee+$gfee;
				$wgow = $wgowfee+$gfee;
				$wgbs = $wgbsfee+$gfee;
				$prow = $prowfee+$gfee;
				$prbs = $prbsfee+$gfee;
				$fitted = $fittedfee-$cost+$gfee;
				$fwgow = $fwgowfee-$wgow+$gfee;
				$fwgbs = $fwgbsfee-$wgbs+$gfee;
				$fprow = $fprowfee-$prow+$gfee;
				$fprbs = $fprbsfee-$prbs+$gfee;
				break;
			case '7':
				// Composite doors
				$option = get_option('lsdsettings_compositedoors');
				$wgowfee = $option['lsdcompositedoors-additionalfees-wgow']+0;
				$wgbsfee = $option['lsdcompositedoors-additionalfees-wgbs']+0;
				$prowfee = $option['lsdcompositedoors-additionalfees-prow']+0;
				$prbsfee = $option['lsdcompositedoors-additionalfees-prbs']+0;
				$glazingoptions_per_style = array(26, 24, 10, 0, 25, 30, 30, 30, 0, 24, 23, 25, 25, 24, 30, 0, 30, 16, 23, 21);
				$glazingfees = array();
				for($i=0; $i<count($glazingoptions_per_style); $i++) {
					if ($glazingoptions_per_style[$i]==0) {
						$tmp = [0,0];
					} else {
						$tmp = array();
						for($j=0; $j<$glazingoptions_per_style[$i]; $j++) {
							array_push($tmp,$option['lsdcompositedoors-glazingoptions-'.($i+1).'-price']['option_'.($j+1)]);
						}
					}
					array_push($glazingfees,$tmp);
				}
				$style = intval($entry[24]);
				if ($style<4) { $glazingopt = intval($entry[44+$style]); }
				if ($style==4) { $glazingopt = intval($entry[63]); }
				if ($style>4 && $style<9) { $glazingopt = intval($entry[44+$style-1]); }
				if ($style==9) { $glazingopt = intval($entry[64]); }
				if ($style>9) { $glazingopt = intval($entry[44+$style-2]); }
				$gfee = $glazingfees[$style-1][$glazingopt-1];
				$cost = $composite_prices[0][$style-1]+$gfee;
				$wgow = $cost+$wgowfee;
				$wgbs = $wgow+$wgbsfee;
				$prow = $cost+$prowfee;
				$prbs = $prow+$prbsfee;
				$fitted = $composite_prices[1][$style-1]-$cost+$gfee;
				$fwgow = $fitted+$wgowfee;
				$fwgbs = $fwgow+$wgbsfee;
				$fprow = $fitted+$prowfee;
				$fprbs = $fprow+$prbsfee;
				break;
		}
	
		?>
		<div id="quote-calculator-overview">
			<h3 style="text-align: center;"><span style="color: #EB7305;">Your Quote Overview</span></h3>
			<p style="text-align:center">
				<span style="color:#fff; font-size:1.5em">Select finish type to see available colours.</span>
			</p>
			<div class="quote-thumbnail">
				<img src="<?php $imgsrc = lsd_get_preview_image($framework,$openers,$layout,$doors,$style,$panes,$glazingopt); echo $imgsrc; ?>">
				<div class="swatch-zoom"></div>
			</div>
			<div class="quote-overview-table">
			<table>
				<tr><td colspan="3"><h4>1. Choose Finish and Delivery Type.<span id="missed-field">Required - Please select a colour</span></h4></td></tr>
				<tr><th style="padding-left:10px">Finish</th><th>Fitted</th><th>Supply Only</th></tr>
				<tr class="active-row">
					<td><span class="finish_txt">White</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">The classic white UPVC window</span></td>
					<td><input id="finish01" type="radio" value="1" name="finish" /> <label for="finish01">&pound; <?php echo number_format($cost+$fitted,0); ?></label></td>
					<td><input id="finish02" type="radio" value="10" name="finish" /> <label for="finish02">&pound; <?php echo number_format($cost,0); ?></label></td>
				</tr>
<?php if ($framework!=7) { ?>				
				<tr class="active-row">
					<td><span class="finish_txt">Classic Woodgrains on White</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Two classic traditional woodgrains that have stood the test of time</span></td>
					<td><input id="finish03" type="radio" value="2" name="finish" /> <label for="finish03">&pound; <?php echo number_format($wgow+$fwgow,0); ?></label></td>
					<td><input id="finish04" type="radio" value="20" name="finish" /> <label for="finish04">&pound; <?php echo number_format($wgow,0); ?></label></td>
				</tr>
<?php }else{?>				
				<tr class="active-row">
					<td><span class="finish_txt">Colours on white</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Two classic traditional woodgrains that have stood the test of time</span></td>
					<td><input id="finish03" type="radio" value="2" name="finish" /> <label for="finish03">&pound; <?php echo number_format($wgow+$fwgow,0); ?></label></td>
					<td><input id="finish04" type="radio" value="20" name="finish" /> <label for="finish04">&pound; <?php echo number_format($wgow,0); ?></label></td>
				</tr>
<?php } ?>
<?php if ($framework!=7) { ?>				
				<tr class="active-row">
					<td><span class="finish_txt">Classic Woodgrains (both sides)</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Two classic traditional woodgrains that have stood the test of time</span></td>
					<td><input id="finish05" type="radio" value="3" name="finish" /> <label for="finish05">&pound; <?php echo number_format($wgbs+$fwgbs,0); ?></label></td>
					<td><input id="finish06" type="radio" value="30" name="finish" /> <label for="finish06">&pound; <?php echo number_format($wgbs,0); ?></label></td>
				</tr>
<?php } ?>
				<tr class="active-row">
					<td><span class="finish_txt">Artisan Woodgrains on White</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Contemporary woodgrains that add a modern twist</span></td>
					<td><input id="finish07" type="radio" value="4" name="finish" /> <label for="finish07">&pound; <?php echo number_format($prow+$fprow,0); ?></label></td>
					<td><input id="finish08" type="radio" value="40" name="finish" /> <label for="finish08">&pound; <?php echo number_format($prow,0); ?></label></td>
				</tr>
<?php if ($framework!=7) { ?>				
				<tr class="active-row">
					<td><span class="finish_txt">Artisan Woodgrains (both sides)</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Contemporary woodgrains that add a modern twist</span></td>
					<td><input id="finish09" type="radio" value="5" name="finish" /> <label for="finish09">&pound; <?php echo number_format($prbs+$fprbs,0); ?></label></td>
					<td><input id="finish10" type="radio" value="50" name="finish" /> <label for="finish10">&pound; <?php echo number_format($prbs,0); ?></label></td>
				</tr>
<?php } ?>
<?php if ($framework!=7) { ?>
				<tr class="active-row">
					<td><span class="finish_txt">Colours on White*</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Exciting new colours for a wow factor</span></td>
					<td><input id="finish11" type="radio" value="6" name="finish" /> <label for="finish11">&pound; <?php echo number_format((($framework==7)?($cost+$fitted+100):($prow+$fprow)),0); ?></label></td>
					<td><input id="finish12" type="radio" value="60" name="finish" /> <label for="finish12">&pound; <?php echo number_format((($framework==7)?($cost+100):$prow),0); ?></label></td>
				</tr>
<?php }else{ ?>				
				<tr class="active-row">
					<td><span class="finish_txt">premium colours on white</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Exciting new colours for a wow factor</span></td>
					<td><input id="finish11" type="radio" value="6" name="finish" /> <label for="finish11">&pound; <?php echo number_format((($framework==7)?($cost+$fitted+100):($prow+$fprow)),0); ?></label></td>
					<td><input id="finish12" type="radio" value="60" name="finish" /> <label for="finish12">&pound; <?php echo number_format((($framework==7)?($cost+100):$prow),0); ?></label></td>
				</tr>
<?php } ?>
<?php if ($framework!=7) { ?>
				<tr class="active-row">
					<td><span class="finish_txt">Colours (both sides)*</span><i class="icon pixicon-help bg-none icon-circle"></i><span class="quote_tooltip hide">Exciting new colours for a wow factor</span></td>
					<td><input id="finish13" type="radio" value="7" name="finish" /> <label for="finish13">&pound; <?php echo number_format((($framework==7)?($cost+$fitted+100+200):($prbs+$fprbs)),0); ?></label></td>
					<td><input id="finish14" type="radio" value="70" name="finish" /> <label for="finish14">&pound; <?php echo number_format(($framework==7)?($cost+100+200):$prbs,0); ?></label></td>
				</tr>
<?php } ?>
				<tr><td colspan="3"><h4>2. Choose Colour<span id="missed-field">Required - Please select a colour</span></h4></td></tr>
				<tr><td colspan="3">
					<p>Colour options available with your selection:</p>
					<span class="swatches"><em>select Finish first to see available options</em></span>
					</td></tr>
				<tr><td colspan="3" class="vat-disclaimer"><em>&mdash; All prices include fitting, but are exclusive of V.A.T. If you require supply only prices, please call us</em></td></tr>
				<tr><td colspan="3" class="vat-disclaimer bgyellow">Please be aware that current legislation may dictate the use of toughened safety glass, trickle ventilation and potential fire egress that is not included in this price</td></tr>
				<tr><td colspan="3"><p><a id="add-to-basket-button" href="" onclick="addToQuoteBasket(); return false;" class="button">Add Item to Quote</a><a id="add-new-quote-button" href="" class="button">Add Additional Item</a><a id="view-cart-button" href="<?php echo get_site_url(); ?>/quote-overview/" class="button">View Quote</a></p></td></tr>
			</table>
			</div>
		</div>
<script type="text/javascript">
var swatches = {
	white: [{ name:'White', url: false, color: '#ffffff' }],
	classic: [
		{ name:'Light Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/light-oak-swatch.jpg', color: false },
		{ name:'Rosewood', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/rosewood-swatch.jpg', color: false }
	],
	artisan: [
		{ name:'Anteak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/anteak-swatch.jpg', color: false },
		{ name:'Dark Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/dark-oak-swatch.jpg', color: false },
		{ name:'English Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/english-oak-swatch.jpg', color: false },
		{ name:'Irish Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/irish-oak-swatch.jpg', color: false },
		{ name:'Mahogany', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/mahogany-swatch.jpg', color: false },
		{ name:'Natural Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/natural-oak-swatch.jpg', color: false },
		{ name:'Rustic Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/rustic-oak-swatch.jpg', color: false },
		{ name:'Siena PN', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/siena-pn-swatch.jpg', color: false },
		{ name:'Swap Oak', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/swamp-oak-swatch.jpg', color: false },
		{ name:'Walnut', url: '<?php echo get_site_url(); ?>/wp-content/uploads/2018/02/walnut-swatch.jpg', color: false }
	],
	colour: [
		{ name:'Agate Grey', url: false, color: '#979484' },
		{ name:'Anthracite Grey', url: false, color: '#27323b' },
		{ name:'Basalty Grey', url: false, color: '#64666b' },
		{ name:'Black Brown', url: false, color: '#1a1612' },
		{ name:'Chartwell Green', url: false, color: '#8da793' },
		{ name:'Cream', url: false, color: '#fff4e3' },
		{ name:'Dark Green', url: false, color: '#1a3223' },
		{ name:'Dark Red', url: false, color: '#62101e' },
		{ name:'Hazy Grey', url: false, color: '#92979b' },
		{ name:'Steel Blue', url: false, color: '#001c3f' },
		{ name:'Duck Egg Blue', url: false, color: '#d4eee8' }
	],
	composite_classic: [
		{ name:'Green', url:false, color:'#1a3223' },
		{ name:'Black', url:false, color:'#1a1612' },
		{ name:'Blue', url:false, color:'#001c3f' },
		{ name:'Red', url:false, color:'#62101e' },
	],
	composite_woodgrain: [
		{ name:'Golden Oak', url:'/wp-content/uploads/2017/10/golden-oak.jpg', color:false },
		{ name:'Rosewood', url:'/wp-content/uploads/2018/02/rosewood-swatch.jpg', color:false },
	],
	composite_prestige: [
		{ name:'Cream', url: false, color: '#fff4e3' },
		{ name:'Chartwell Green', url: false, color: '#8da793' },
		{ name:'Duck Egg Blue', url: false, color: '#d4eee8' },
		{ name:'Anthracite Grey', url: false, color: '#27323b' },
	]
};
var selectedSwatch = "";

jQuery(document).ready(function(){
	scrollToSection("quote-calculator-overview");
	jQuery(".active-row input").click(function(){
		jQuery(".active-row .finish_txt, .active-row label, .active-row i").css({color:'#999'});
		jQuery(this).parent().parent().find(".finish_txt, label, i").stop().css({color:'#EB7305'});
		jQuery('body,html').animate({scrollTop:jQuery('table h4').eq(1).offset().top-150});
		var finish = jQuery(this).val()*1;
		var str = "";
		var framework = <?php echo $framework; ?>;
		var swatchType;
		switch(finish) {
			case 1:
			case 10:
				swatchType="white";
				break;
			case 2:
			case 3:
			case 20:
			case 30:
				swatchType=(framework==7?"composite_classic":"classic");
				break;
			case 4:
			case 5:
			case 40:
			case 50:
				swatchType=(framework==7?"composite_woodgrain":"artisan");
				break;
			case 6:
			case 7:
			case 60:
			case 70:
				swatchType=(framework==7?"composite_prestige":"colour");
				break;
			default:
				swatchType="white";
		}
		var totswatches = swatches[swatchType].length;
		if (framework !=4 && framework !=7) { // windows, french doors and profile doors
			if (swatchType=="colour") { totswatches-=1; }
			for (var i=0; i<totswatches; i++) {
				var optname = swatches[swatchType][i].name;
				var opturl = swatches[swatchType][i].url;
				var opthex = swatches[swatchType][i].color;
				str += '<label><input type="radio" name="coloroption" value="'+optname+'">';
				if (opturl != false) {
					str += '<img src="'+opturl+'">';
				} else {
					str += '<span style="background-color:'+opthex+'"></span>';
				}
				str += '</label>';
			}
		} else {
			if (swatchType=="white" || swatchType=="classic" || swatchType=="composite_classic") {
				for (var i=0; i<totswatches; i++) {
					var optname = swatches[swatchType][i].name;
					var opturl = swatches[swatchType][i].url;
					var opthex = swatches[swatchType][i].color;
					str += '<label><input type="radio" name="coloroption" value="'+optname+'">';
					if (opturl != false) {
						str += '<img src="'+opturl+'">';
					} else {
						str += '<span style="background-color:'+opthex+'"></span>';
					}
					str += '</label>';
				}
			} else {
                if (swatchType=="artisan") {
                    var optname = swatches[swatchType][3].name;
                    var opturl = swatches[swatchType][3].url;
                    var opthex = swatches[swatchType][3].color;
                    str += '<label><input type="radio" name="coloroption" value="'+optname+'">';
                    if (opturl != false) { str += '<img src="'+opturl+'">'; } else { str += '<span style="background-color:'+opthex+'"></span>'; }
                    str += '</label>';
                    optname = swatches[swatchType][4].name;
                    opturl = swatches[swatchType][4].url;
                    opthex = swatches[swatchType][4].color;
                    str += '<label><input type="radio" name="coloroption" value="'+optname+'">';
                    if (opturl != false) { str += '<img src="'+opturl+'">'; } else { str += '<span style="background-color:'+opthex+'"></span>'; }
                    str += '</label>';
                } else {
                    for (var i=0; i<totswatches; i++) {
                        if (framework!=7) {
                            if (i==0 || i==2 || i==8) { continue; }
                            if (i==5 && framework==4) { continue; }
                            if (i==totswatches-1) { continue; }
                        }
                        var optname = swatches[swatchType][i].name;
                        var opturl = swatches[swatchType][i].url;
                        var opthex = swatches[swatchType][i].color;
                        str += '<label><input type="radio" name="coloroption" value="'+optname+'">';
                        if (opturl != false) {
                            str += '<img src="'+opturl+'">';
                        } else {
                            str += '<span style="background-color:'+opthex+'"></span>';
                        }
                        str += '</label>';
                    }
                }
			}
		}
		jQuery(".swatches").html(str);
		selectedSwatch = "";
		jQuery(".swatch-zoom").stop().fadeOut().html("");
	});
	jQuery(".swatches label").live({
		mouseenter: function(){
			var name = jQuery(this).find("input").val();
			var url = jQuery(this).find("img").attr("src");
			if (!url) {
				var col = jQuery(this).find("span").css("background-color");
				jQuery(".swatch-zoom").html('<p>'+name+'</p><span style="background-color:'+col+'"></span>').stop().fadeIn();
			} else {
				jQuery(".swatch-zoom").html('<p>'+name+'</p><img src="'+url+'">').stop().fadeIn();
			}
		},
		mouseleave: function(){
			if (jQuery(".swatches input:checked").val()!="") {
				jQuery(".swatch-zoom").html(selectedSwatch);
			} else {
				jQuery(".swatch-zoom").stop().fadeOut().html("");
			}
		},
		click: function(){
			jQuery(".swatches label").stop().animate({opacity:0.5});
			jQuery(this).stop().animate({opacity:1});
			var name = jQuery(this).find("input").val();
			var url = jQuery(this).find("img").attr("src");
			if (!url) {
				var col = jQuery(this).find("span").css("background-color");
				selectedSwatch = '<p>'+name+'</p><span style="background-color:'+col+'"></span>';
			} else {
				selectedSwatch = '<p>'+name+'</p><img src="'+url+'">';
			}
		}
	});
	
	jQuery( '.icon.pixicon-help.bg-none.icon-circle' ).hover(
		function() {
			var tooltip = jQuery(  this ).siblings( '.quote_tooltip' );
			tooltip.fadeIn( 'slow' );
			tooltip.removeClass( 'hide' );
		},
		function() {
			var tooltip = jQuery(  this ).siblings( '.quote_tooltip' );
			tooltip.fadeOut( 'slow' );
			tooltip.addClass( 'hide' );
		}
	);
});

function addToQuoteBasket(){
	var ajaxurl = "<?php echo get_site_url(); ?>/wp-admin/admin-ajax.php";
	var finish = jQuery("input[name=finish]:checked").val();
	var color = jQuery("input[name=coloroption]:checked").val();
	if (!finish) {
jQuery(".quote-overview-table h4:eq(0)").css({"color":"#F30000", "font-weight":"bold"});
		jQuery (".quote-overview-table h4:eq(0) #missed-field").css({"display":"inline-block"});	} else {
		jQuery(".quote-overview-table h4:eq(0)").css('color','#FFF');
		jQuery (".quote-overview-table h4:eq(0) #missed-field").css({"display":"none"});	
	}
	if (!color) {
		jQuery(".quote-overview-table h4:eq(1)").css({"color":"#F30000", "font-weight":"bold"});
		jQuery (".quote-overview-table h4:eq(1) #missed-field").css({"display":"inline-block"});
	} else {
		jQuery(".quote-overview-table h4:eq(1)").css('color','#FFF');
		jQuery (".quote-overview-table h4:eq(1) #missed-field").css({"display":"none"});	
	}
	if (!finish || !color) { return false; }
	var params = {
		action: "lsd_add_to_quote_basket",
		image: "<?php echo $imgsrc; ?>",
		fitted: "<?php echo $cost+$fitted; ?>",
		cost: "<?php echo $cost; ?>",
		wgowf: "<?php echo $wgow+$fwgow; ?>",
		wgow: "<?php echo $wgow; ?>",
		wgbsf: "<?php echo $wgbs+$fwgbs; ?>",
		wgbs: "<?php echo $wgbs; ?>",
		prowf: "<?php echo $prow+$fprow; ?>",
		prow: "<?php echo $prow; ?>",
		prbsf: "<?php echo $prbs+$fprbs; ?>",
		prbs: "<?php echo $prbs; ?>",
		framework: "<?php echo $framework; ?>",
		openers: "<?php echo $openers; ?>",
		layout: "<?php echo $layout; ?>",
		doors: "<?php echo $doors; ?>",
		style: "<?php echo $style; ?>",
		panes: "<?php echo $panes; ?>",
		width: "<?php echo $width; ?>",
		height: "<?php echo $height; ?>",
		choice: finish,
		color: color
	};
	jQuery("#add-to-basket-button").fadeOut();
	jQuery.post(
		ajaxurl,
		params,
		function (result, status){
			jQuery("#add-new-quote-button, #view-cart-button").fadeIn().css("display","inline-block");
		}
	);
}

function mailQuote(){
	var email = jQuery("input[type=email]").val();
	if (email=="") {
		jQuery("input[type=email]").css({borderColor: "#F30000"});
	} else {
		jQuery("input[type=email]").css({borderColor: "inherit"});
		var ajaxurl = "<?php echo get_site_url(); ?>/wp-admin/admin-ajax.php";
		var params = {
			action: "lsd_mail_single_quote",
			sendto: email,
			image: "<?php echo $imgsrc; ?>",
			fitted: "<?php echo $cost+$fitted; ?>",
			cost: "<?php echo $cost; ?>",
			wgowf: "<?php echo $wgow+$fwgow; ?>",
			wgow: "<?php echo $wgow; ?>",
			wgbsf: "<?php echo $wgbs+$fwgbs; ?>",
			wgbs: "<?php echo $wgbs; ?>",
			prowf: "<?php echo $prow+$fprow; ?>",
			prow: "<?php echo $prow; ?>",
			prbsf: "<?php echo $prbs+$fprbs; ?>",
			prbs: "<?php echo $prbs; ?>",
			framework: "<?php echo $framework; ?>",
			openers: "<?php echo $openers; ?>",
			layout: "<?php echo $layout; ?>",
			doors: "<?php echo $doors; ?>",
			style: "<?php echo $style; ?>",
			panes: "<?php echo $panes; ?>",
			width: "<?php echo $width; ?>",
			height: "<?php echo $height; ?>",
			choice: jQuery("input[name=finish]:checked").val(),
			color: jQuery("input[name=coloroption]:checked").val()
		};
		jQuery.post(
			ajaxurl,
			params,
			function (result, status) {
				jQuery(".full-quote-table").parent().prepend("<div><p style='font-size:1.2em; color:#fff'>The quote has been sent to the email address you&rsquo;ve provided.</p></div>");
				jQuery("html,body").animate({scrollTop:0});
			}
		);
	}
}
</script>
		<?php
	}
}
add_action( 'gform_after_submission', 'lsd_gform_after_submission', 10, 2 );
