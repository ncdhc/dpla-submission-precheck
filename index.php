<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href='http://fonts.googleapis.com/css?family=Playfair+Display:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
        <title>DPLA Submission Pre&middot;Check</title>

        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php
        $provider = "North Carolina Digital Heritage Center"; // DPLA Service Hub Name
        $oaibaseurl = "http://brevard.lib.unc.edu:8080/repox/OAIHandler"; // DPLA Service Hub Base URL
        $metadataprefix = "MODS";
        $harvestday = "15"; // Day of the month DPLA is scheduled to harvest
        $dataprovider = isset($_GET['dataprovider']) ? $_GET['dataprovider'] : '';
        $helpcontact = "Lisa Gregory";
        $helpemail = "gregoryl@email.unc.edu";
        $helpphone = "(919) 962-4839";

        function getSets($rt) {
            global $oaibaseurl;
            if ($rt !== '') {
                $seturl = $oaibaseurl . "?verb=ListSets&resumptionToken=" . $rt;
            } else {
                $seturl = $oaibaseurl . "?verb=ListSets";
            }

            // create curl resource
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $seturl);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string
            $output = curl_exec($ch);

            $output = str_replace("oai:", "", $output);

            // close curl resource to free up system resources
            curl_close($ch);

            try {
                $setxml = new SimpleXMLElement($output);
                return $setxml;
            } catch (Exception $e) {
                
            }
        }

        $setarray = array();

        function processSets($setxml, $dataprovider) {
            global $setarray;
            $setcount = count($setxml->ListSets->set);

            // build an array of sets to work with
            for ($i = 0; $i < $setcount; $i++) {
                // limit the sets processed to those beginning with this data provider's prefix
                if (substr($setxml->ListSets->set[$i]->setSpec, 0, strlen($dataprovider . "_")) === $dataprovider . "_") {
                    $setarray[] = $setxml->ListSets->set[$i]->setName . "|" . $setxml->ListSets->set[$i]->setSpec . "|" . ($i + 1);
                }
            }

            // loop through records again until the end of the set is reached
            if (isset($setxml->ListSets->resumptionToken)) {
                if ($setxml->ListSets->resumptionToken == '') {
                    // do nothing
                } else {
                    $nextpass = getSets($setxml->ListSets->resumptionToken);
                    processSets($nextpass);
                }
            }
        }
        ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="text-muted"><em><?php echo $provider; ?></em></h3>
                    <h1>DPLA Submission Pre&middot;Check</h1>
                    <br>

<?php if (empty($dataprovider)) { ?>

                        <div class="alert alert-danger"><span class="glyphicon glyphicon-info-sign"></span><strong> No valid Data Provider indicated.</strong></div>

<?php
} else {

    $setxml = getSets('');
    processSets($setxml, $dataprovider);
    sort($setarray);


    if (empty($setarray)) {
        ?>

                            <div class="alert alert-danger"><span class="glyphicon glyphicon-info-sign"></span><strong> No valid Data Provider indicated.</strong></div>

                        <?php } else { ?>


                            <div class="alert alert-warning">

                                <p><span class="glyphicon glyphicon-info-sign"></span> The next DPLA Harvest is scheduled for <strong>Wednesday, May 14, 2014</strong>.</p>


                                <form class="form-inline">
                                    <input type="hidden" name="dataprovider" value="<?php echo $dataprovider; ?>"/>

                                    <div class="form-group">

                                        <select class="form-control" id="setselect" name="set">
                                            <option value="">Select a Set</option>
                                            <?php 
                                            $prettysetarray= array();
                                            foreach ($setarray as $sethash) {
                                                $setparts = explode("|", $sethash);
                                                $prettysetarray[$setparts[1]] = $setparts[0];
                                                ?>

                                                <option value="<?php echo $setparts[1]; ?>"><?php echo $setparts[0]; ?> (<?php echo $setparts[1]; ?>)</option>
                                            <?php } ?>


                                        </select>

                                    </div>

                                    <button type="submit" class="btn btn-default">Submit</button>

                                </form>

                            </div>

                            <?php
                            $set = isset($_GET['set']) ? $_GET['set'] : '';

                            // make sure that the indicated set exists in the set array
                            $setstring = implode('', $setarray);
                            $setcheck = "|" . $set . "|";

                            if (stristr($setstring, $setcheck) === FALSE) {
                                ?>

                                <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span><strong> Please choose a Data Set.</strong></div>

                            <?php } else { ?>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                $setname = $prettysetarray[$set];
                                ?>
                                <h2>Analysis <span class="small text-muted"><?php echo $setname;?></span></h2>
                                
                                <?php
                                
                                
                                $feedURL = $oaibaseurl . "?verb=ListRecords&set=" . $set . "&metadataPrefix=".$metadataprefix;
                                $recordxml = '';

                                function getAnalysis($feedURL) {

                                    global $recordxml;
                                    global $oaibaseurl;

                                    // create curl resource
                                    $ch = curl_init();

                                    // set url
                                    curl_setopt($ch, CURLOPT_URL, $feedURL);

                                    //return the transfer as a string
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                    // $output contains the output string
                                    $pageoutput = curl_exec($ch);

                                    // close curl resource to free up system resources
                                    curl_close($ch);

                                    try {
                                        $pagexml = new SimpleXMLElement($pageoutput);
                                    } catch (Exception $e) {
                                        
                                    }

                                    $xml = new DOMDocument;
                                    if (@$xml->load($feedURL) === false) {
                                        echo "<p>Please enter a valid feed URL.</p>";
                                    } else {
                                        $xsl = new DOMDocument;
                                        $xslpath = 'xsl/analysis.xsl';
                                        $xsl->load($xslpath);
                                        $proc = new XSLTProcessor;
                                        $proc->importStylesheet($xsl);


                                        $result = trim($proc->transformToXML($xml));

                                        $recordxml .= $result;
                                     
                                    }
                                    
                                    if (isset($pagexml->ListRecords->resumptionToken)) {
                                    $nextfeedURL = $oaibaseurl . "?verb=ListRecords&resumptionToken=" . $pagexml->ListRecords->resumptionToken;
                                    getAnalysis($nextfeedURL);
                                    }
                                }

                                getAnalysis($feedURL);


                               
                                $analysis = simplexml_load_string("<results>" . $recordxml . "</results>");
                                
                       

                                $ageo = array();
                                $athumburl = array();
                                $adate = array();
                                $atype = array();
                                foreach ($analysis->record as $arec) {
                                    if (isset($arec->geo)) {
                                        $ageo[] = (string) $arec->url . "||" . $arec->title . "||" . $arec->oai_id;
                                    }
                                    if (isset($arec->thumburl)) {
                                        $athumburl[] = (string) $arec->url . "||" . $arec->title. "||" . $arec->oai_id;
                                    }
                                    if (isset($arec->type)) {
                                        $atype[] = (string) $arec->url . "||" . $arec->title. "||" . $arec->oai_id;
                                    }
                                    if (isset($arec->date)) {
                                        $adate[] = (string) $arec->url . "||" . $arec->title. "||" . $arec->oai_id;
                                    }
                                }
                                ?>
                                <div class="panel-group" id="accordion">

                                    <?php if (!empty($athumburl)) { ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                                        <?php echo count($athumburl); ?> records in this set are missing thumbnail images.
                                                    </a>
                                                    <a class="helpinfo" data-toggle="popover" data-content="These records will display a default thumbnail on the DPLA site."><span class="glyphicon glyphicon-question-sign"></span></a>
                                                </h4>
                                            </div>
                                            <div id="collapseOne" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <ul>
                                                        <?php
                                                        foreach ($athumburl as $aitem) {
                                                            $aitemparts = explode("||", $aitem);
                                                            ?>

                                                            <li><a target="_blank" href="<?php echo $aitemparts[0]; ?>"><?php echo $aitemparts[1]; ?></a> &#x2013; <a span="text-muted" target="_blank" href="http://brevard.lib.unc.edu:8080/repox/OAIHandler?verb=GetRecord&metadataPrefix=MODS&identifier=<?php echo $aitemparts[2];?>">OAI Record</a></li>

                                                        <?php } ?> 
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                 <?php } if (!empty($ageo)) { ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                                        <?php echo count($ageo); ?> records in this set are missing geographic data.
                                                    </a>
                                                    <a class="helpinfo" data-toggle="popover" data-content="These records will not appear in map searches."><span class="glyphicon glyphicon-question-sign"></span></a>
                                                </h4>

                                            </div>
                                            <div id="collapseTwo" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <ul>
                                                        <?php
                                                        foreach ($ageo as $aitem) {
                                                            $aitemparts = explode("||", $aitem);
                                                            ?>

                                                            <li><a target="_blank"  href="<?php echo $aitemparts[0]; ?>"><?php echo $aitemparts[1]; ?></a> &#x2013; <a span="text-muted" target="_blank" href="http://brevard.lib.unc.edu:8080/repox/OAIHandler?verb=GetRecord&metadataPrefix=MODS&identifier=<?php echo $aitemparts[2];?>">OAI Record</a></li>

                                                             <?php } ?> 
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                         <?php } if (!empty($atype)) { ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                                    <?php echo count($atype); ?> records in this set are missing 'type' information.
                                                    </a>
                                                    <a class="helpinfo" data-toggle="popover" data-content="These records will not appear when users limit a search using the 'type' facet."><span class="glyphicon glyphicon-question-sign"></span></a>
                                                </h4>
                                            </div>
                                            <div id="collapseThree" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <ul>
                                                        <?php
                                                        foreach ($atype as $aitem) {
                                                            $aitemparts = explode("||", $aitem);
                                                            ?>

                                                            <li><a target="_blank"  href="<?php echo $aitemparts[0]; ?>"><?php echo $aitemparts[1]; ?></a> &#x2013; <a span="text-muted" target="_blank" href="http://brevard.lib.unc.edu:8080/repox/OAIHandler?verb=GetRecord&metadataPrefix=MODS&identifier=<?php echo $aitemparts[2];?>">OAI Record</a></li>

                                                            <?php } ?> 
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
            <?php } if (!empty($adate)) { ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                <?php echo count($adate); ?> records in this set are missing date information.
                                                    </a>
                                                    <a class="helpinfo" data-toggle="popover" data-content="These records will not appear on DPLA timelines."><span class="glyphicon glyphicon-question-sign"></span></a>
                                                </h4>
                                            </div>
                                            <div id="collapseFour" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <ul>
                <?php
                foreach ($adate as $aitem) {
                    $aitemparts = explode("||", $aitem);
                    ?>

                                                            <li><a target="_blank" href="<?php echo $aitemparts[0]; ?>"><?php echo $aitemparts[1]; ?></a> &#x2013; <a span="text-muted" target="_blank" href="http://brevard.lib.unc.edu:8080/repox/OAIHandler?verb=GetRecord&metadataPrefix=MODS&identifier=<?php echo $aitemparts[2];?>">OAI Record</a></li>

                                        <?php } ?> 
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
            <?php } ?>
                                </div>

                                <?php if(empty($adate)&&empty($ageo)&&empty($athumburl)&&empty($atype)) { ?>
                                
                                <h4 class="text-muted"><em>Records are complete. No missing data!</em></h4>
                                <?php } ?>

                            </div>
                        </div>
                        <hr>
                        
                        
                        
                        <?php
                        // get a random item id from the first page of OAI ListIdentifiers response and use 
                        // that record to populate sample record display
                        
                        $listidurl = $oaibaseurl."?verb=ListIdentifiers&set=".$set."&metadataPrefix=".$metadataprefix;
                         // create curl resource
                        $ch = curl_init();

                        // set url
                        curl_setopt($ch, CURLOPT_URL, $listidurl);

                        //return the transfer as a string
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        // $output contains the output string
                        $idoutput = curl_exec($ch);

                        // close curl resource to free up system resources
                        curl_close($ch);

                        $idarray = simplexml_load_string($idoutput);
                        $idmaxnum = count($idarray->ListIdentifiers->header);
                        $idrandnum = rand(0,$idmaxnum);
                        $sampleid = $idarray->ListIdentifiers->header[$idrandnum]->identifier;

                        
                        $sampleurl = $oaibaseurl."?verb=GetRecord&identifier=".$sampleid."&metadataPrefix=".$metadataprefix;
                         // create curl resource
                        $ch = curl_init();

                        // set url
                        curl_setopt($ch, CURLOPT_URL, $sampleurl);

                        //return the transfer as a string
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        // $output contains the output string
                        $sampleoutput = curl_exec($ch);

                        // close curl resource to free up system resources
                        curl_close($ch);
                        
                        
                        try {
                            $samplexml = new SimpleXMLElement($sampleoutput);
                        } catch (Exception $e) {

                        }

                       
                            $samplexsl = new DOMDocument;
                            $samplexslpath = 'xsl/samplerecord.xsl';
                            $samplexsl->load($samplexslpath);
                            $sampleproc = new XSLTProcessor;
                            $sampleproc->importStylesheet($samplexsl);


                            $sampleresult = trim($sampleproc->transformToXML($samplexml));

                       
                        $samplearray = simplexml_load_string($sampleresult);
            

                        
                        $oai_id = isset($samplearray->oai_id) ? $samplearray->oai_id : '';
                        $title = isset($samplearray->title) ? $samplearray->title : '';
                        $url = isset($samplearray->url) ? $samplearray->url : '';
                        $thumburl = isset($samplearray->thumburl) ? $samplearray->thumburl : '';
                        $rights = isset($samplearray->rights) ? $samplearray->rights : '';
                        $type = isset($samplearray->type) ? $samplearray->type : '';
                        $description = isset($samplearray->description) ? $samplearray->description : '';
                        $contributing_institution = isset($samplearray->contributing_institution) ? $samplearray->contributing_institution : '';
                        $creator = isset($samplearray->creator->data) ? (array) $samplearray->creator->data : array();
                        $date = isset($samplearray->date->data) ? (array) $samplearray->date->data : array();
                        $publisher = isset($samplearray->publisher->data) ? (array) $samplearray->publisher->data : array();
                        $location = isset($samplearray->location->data) ? (array) $samplearray->location->data : array();
                        $subject = isset($samplearray->subject->data) ? (array) $samplearray->subject->data : array();
                        

            
                        ?>
                        
                        
                        
                        
                        
                        <div class="row">
                            <div class="col-md-6"><h2>Sample Record 
                                    <span class="small">
                                        <a class="helpinfo" data-toggle="popover" data-content="This is an example of how your content will appear on the DPLA's web site.">
                                            <span class="glyphicon glyphicon-question-sign"></span>
                                        </a>
                                    </span>
                                </h2></div>
                            <div class="col-md-6"><a class="margintop btn btn-default pull-right" onclick="window.location.replace(window.location.href);">Load Another Record</a></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="itemcard">
                                    <h3><?php echo $title;?></h3>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img class="img-responsive preview" src="<?php echo $thumburl;?>"/>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>
                                                        <tr><th>Creator</th><td><?php foreach( $creator as $singlecreator) { echo $singlecreator."<br/>"; };?></td></tr>
                                                        <tr><th>Created Date</th><td><?php foreach( $date as $singledate) { echo $singledate."<br/>"; };?></td></tr>
                                                        <tr><th>Partner</th><td><?php echo $provider;?></td></tr>
                                                        <tr><th>Contributing Institution</th><td><?php echo $contributing_institution;?></td></tr>
                                                        <tr><th>Publisher</th><td><?php foreach( $publisher as $singlepub) { echo $singlepub."<br/>"; };?></td></tr>
                                                        <tr><th>Description</th><td><?php echo $description;?></td></tr>
                                                        <tr><th>Location</th><td><?php foreach( $location as $singleloc) { echo $singleloc."<br/>"; };?></td></tr>
                                                        <tr><th>Type</th><td><?php echo $type;?></td></tr>
                                                        <tr><th>Subject</th><td><?php foreach( $subject as $singlesub) { echo $singlesub."<br/>"; };?></td></tr>
                                                        <tr><th>Rights</th><td><?php echo $rights;?></td></tr>
                                                        <tr><th>URL</th><td><a href="<?php echo $url;?>"><?php echo $url;?></a></td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Geographic Data
                                    <span class="small">
                                        <a class="helpinfo" data-toggle="popover" data-content="This section shows how the DPLA will parse the geographic information supplied for this record.">
                                            <span class="glyphicon glyphicon-question-sign"></span>
                                        </a>
                                    </span>
                                </h2>
                                <div class="geodata itemcard">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr><th>Your Data</th><th></th><th>DPLA-parsed Spatial Data</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($location as $georow) { ?>
                                                <tr><th><?php echo $georow;?></th><td><em class="text-muted">becomes</em></td><td>

                                                        <table>
                                                            <tr><th>Display Name</th><td>Davidson County (N.C.)</td></tr>
                                                            <tr><th>Country*</th><td>United States</td></tr>
                                                            <tr><th>State*</th><td>North Carolina</td></tr>
                                                            <tr><th>County*</th><td>Davidson County</td></tr>
                                                            <tr><th>City*</th><td></td></tr>
                                                            <tr><th>Coordinates*</th><td>35.793170929, -80.2127532959</td></tr>
                                                        </table>

                                                    </td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <p><em class="text-muted">* These values are used by <a href="http://dp.la/info/developers/codex/">DPLA API projects</a> and <a href="http://dp.la/map">DPLA's "Explore by Map" interface</a>.</em></p> 

                                </div>
        <?php }
    }
} ?>
                                <hr>
                                <p class='text-muted'>Questions? Email <a href='mailto:<?php echo $helpemail;?>'><?php echo $helpcontact;?></a> or call <?php echo $helpphone;?>.</p>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <script type='text/javascript'>
            $('.helpinfo').popover({
                trigger: 'hover',
                placement: 'right',
                container: 'body'
            });
        </script>
    </body>
</html>