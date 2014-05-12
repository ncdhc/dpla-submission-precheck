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
        
        <div class="container-fluid">
        
          <?php
          // include application-wide config information
          include('config.php');
          
          // make sure set information is present
          $set = isset($_GET['set']) ? $_GET['set'] : '';
          
          if(empty($set)){
              // do nothing
          } else {
          

            $listidurl = $oaibaseurl."?verb=ListIdentifiers&set=".$set."&metadataPrefix=".$metadataprefix;
            $idarray = '';

            function getIdList($url) {
                // establish function to get this set's oai IDs
                global $idarray;
                global $oaibaseurl;

                // create curl resource
                $ch = curl_init();

                // set url
                curl_setopt($ch, CURLOPT_URL, $url);

                //return the transfer as a string
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                // $output contains the output string
                $idoutput = curl_exec($ch);

                // close curl resource to free up system resources
                curl_close($ch);


                try {
                    $pagexml = new SimpleXMLElement($idoutput);
                } catch (Exception $e) {

                }

                if(isset($pagexml->ListIdentifiers)){

                    // write all identifiers that do not have status markers to an array
                    foreach($pagexml->ListIdentifiers->header as $identry){
                        if(!isset($identry['status'])){
                            $idarray[] = (string) $identry->identifier;
                        }

                    }

                    // if a resumption token is set loop through the next page of results
                    if (isset($pagexml->ListIdentifiers->resumptionToken)) {
                    $nextlistidurl = $oaibaseurl . "?verb=ListIdentifiers&resumptionToken=" . $pagexml->ListIdentifiers->resumptionToken;
                    getIdList($nextlistidurl);
                    }

                    }
            }

            getIdList($listidurl);

            // get a random id from the list of ids
            $idmaxnum = count($idarray)-1;
            $idrandnum = rand(0,$idmaxnum);
            $sampleid = $idarray[$idrandnum];

            // fetch that id's record to output as a sample
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

            // transform the sample record to make data easier to address
            $samplexsl = new DOMDocument;
            $samplexslpath = 'xsl/samplerecord.xsl';
            $samplexsl->load($samplexslpath);
            $sampleproc = new XSLTProcessor;
            $sampleproc->importStylesheet($samplexsl);

            $sampleresult = trim($sampleproc->transformToXML($samplexml));

            $samplearray= simplexml_load_string($sampleresult);

            // vars with one value
            $oai_id = isset($samplearray->oai_id) ? $samplearray->oai_id : '';
            $title = isset($samplearray->title) ? $samplearray->title : '';
            $url = isset($samplearray->url) ? $samplearray->url : '';
            $thumburl = !empty($samplearray->thumburl) ? $samplearray->thumburl : 'img/thumbnail.png';
            $rights = isset($samplearray->rights) ? $samplearray->rights : '';
            $type = isset($samplearray->type) ? $samplearray->type : '';
            $description = isset($samplearray->description) ? $samplearray->description : '';
            $contributing_institution = isset($samplearray->contributing_institution) ? $samplearray->contributing_institution : '';

            // vars with multiple values, potentially
            $creator = isset($samplearray->creator->data) ? $samplearray->creator->data : array();
            $date = isset($samplearray->date->data) ? $samplearray->date->data : array();
            $publisher = isset($samplearray->publisher->data) ? $samplearray->publisher->data : array();
            $location = isset($samplearray->location->data) ? $samplearray->location->data : array();
            $subject = isset($samplearray->subject->data) ? $samplearray->subject->data : array();

            // attempt to post-process date information like DPLA does
            $datearray=array();
            foreach($date as $singledate){
                $datearray[] = (string)$singledate;
            }
            if(!empty($datearray)){
            if(count($datearray)>1){
                $mindate = min($datearray);
                $maxdate = max($datearray);
                $postprocdate = $mindate."-".$maxdate;
            } else {
                $postprocdate = $datearray[0];
            }
            } else {
                $postprocdate = '';
            }
           
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
                                                         <?php if(!empty($creator)) { ?>
                                                        <tr><th>Creator</th><td><?php foreach( $creator as $singlecreator) { echo $singlecreator."<br/>"; };?></td></tr>

                                                        <?php } ?>
                                                        
                                                        <?php if(!empty($date)) { ?>
                                                        <!--<tr><th>Created Date</th><td><?php foreach( $date as $singledate) { echo $singledate."<br/>"; };?></td></tr>-->
                                                        <tr><th>Created Date  <a class="helpinfo text-muted" data-toggle="popover" data-content="The DPLA will attempt to normalize dates as records are harvested.">
                                            <span class="glyphicon glyphicon-question-sign"></span>
                                        </a></th><td><?php echo $postprocdate;?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if($provider!=='') { ?>
                                                        <tr><th>Partner</th><td><?php echo $provider;?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if((string) $contributing_institution!=='') { ?>
                                                        <tr><th>Contributing Institution</th><td><?php echo $contributing_institution;?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if(!empty($publisher)) { ?>
                                                        <tr><th>Publisher</th><td><?php foreach( $publisher as $singlepub) { echo $singlepub."<br/>"; };?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if((string) $description!=='') { ?>
                                                        <tr><th>Description</th><td><?php echo $description;?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if(!empty($location)) { ?>
                                                        <tr><th>Location  <a class="helpinfo text-muted" data-toggle="popover" data-content="The DPLA will attempt to normalize/geocode locations as records are harvested.">
                                            <span class="glyphicon glyphicon-question-sign"></span>
                                        </a></th><td><?php foreach( $location as $singleloc) { echo $singleloc."<br/>"; };?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if((string) $type!=='') { ?>
                                                        <tr><th>Type</th><td><?php echo $type;?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if(!empty($subject)) { ?>
                                                        <tr><th>Subject</th><td><?php foreach( $subject as $singlesub) { echo $singlesub."<br/>"; };?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if((string) $rights!=='') { ?>
                                                        <tr><th>Rights</th><td><?php echo $rights;?></td></tr>
                                                        <?php } ?>
                                                        
                                                        <?php if((string) $url!=='') { ?>
                                                        <tr><th>URL</th><td><a target="_blank" href="<?php echo $url;?>"><?php echo $url;?></a></td></tr>
                                                        <?php } ?>
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
                                                <?php 
                                                
                                                // attempt to post-process location data like DPLA does
                                                
                                                foreach($location as $georow) { 
                                                    
                                                    $bingapiurl = "http://dev.virtualearth.net/REST/v1/Locations?culture=en-GB&q=".rawurlencode($georow)."?maxRes=1&incl=queryParse&key=$bingapikey";
                                                    // create curl resource
                                                    $bch = curl_init();

                                                    // set url
                                                    curl_setopt($bch, CURLOPT_URL, $bingapiurl);

                                                    //return the transfer as a string
                                                    curl_setopt($bch, CURLOPT_RETURNTRANSFER, 1);

                                                    // $output contains the output string
                                                    $bingapioutput = curl_exec($bch);

                                                    // close curl resource to free up system resources
                                                    curl_close($bch);
                                                                                                      
                                                    $bingapiarray = json_decode($bingapioutput);
         
                                                    ?>
      
                                                <tr><th><?php echo $georow;?></th><td><em class="text-muted">becomes</em></td><td>

                                                        <table>
                                                            <tr><th>Display Name</th><td><?php echo $georow;?></td></tr>
                                                            <tr><th>Country*</th><td><?php echo isset($bingapiarray->resourceSets[0]->resources[0]->address->countryRegion) ? $bingapiarray->resourceSets[0]->resources[0]->address->countryRegion : '';?></td></tr>
                                                            <tr><th>State*</th><td><?php echo isset($bingapiarray->resourceSets[0]->resources[0]->address->adminDistrict) ? $bingapiarray->resourceSets[0]->resources[0]->address->adminDistrict : '';?></td></tr>
                                                            <tr><th>County*</th><td><?php echo isset($bingapiarray->resourceSets[0]->resources[0]->address->adminDistrict2) ? $bingapiarray->resourceSets[0]->resources[0]->address->adminDistrict2 : '';?></td></tr>
                                                            <tr><th>City*</th><td><?php echo isset($bingapiarray->resourceSets[0]->resources[0]->address->locality) ? $bingapiarray->resourceSets[0]->resources[0]->address->locality : '';?></td></tr>
                                                            <tr><th>Coordinates*</th><td><?php echo isset($bingapiarray->resourceSets[0]->resources[0]->geoCodePoints[0]->coordinates) ? $bingapiarray->resourceSets[0]->resources[0]->geoCodePoints[0]->coordinates[0].", ".$bingapiarray->resourceSets[0]->resources[0]->geoCodePoints[0]->coordinates[1] : '';?></td></tr>
                                                        </table>
                                                        
                                                    </td><td><a class="btn btn-sm btn-default" href="<?php echo $bingapiurl;?>" target="_blank">Bing API URL</a></td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <p><em class="text-muted">* These values are used by <a href="http://dp.la/info/developers/codex/">DPLA API projects</a> and <a href="http://dp.la/map">DPLA's "Explore by Map" interface</a>.</em></p> 
                                </div>
                              
                </div>
            </div>
          <?php    } ?>
        </div>
        <br/>
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