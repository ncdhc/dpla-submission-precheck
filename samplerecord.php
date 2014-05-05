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
          
          include('config.php');
          
          $set = isset($_GET['set']) ? $_GET['set'] : '';
          
          if(empty($set)){
             ?>
              <div class="alert alert-danger"><span class="glyphicon glyphicon-info-sign"></span><strong> No valid Data Set Provided.</strong></div>
              
              
          <?php } else {
          
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
                        $idmaxnum = count($idarray->ListIdentifiers->header)-1;
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
                        $thumburl = !empty($samplearray->thumburl) ? $samplearray->thumburl : 'img/thumbnail.png';
                        $rights = isset($samplearray->rights) ? $samplearray->rights : '';
                        $type = isset($samplearray->type) ? $samplearray->type : '';
                        $description = isset($samplearray->description) ? $samplearray->description : '';
                        $contributing_institution = isset($samplearray->contributing_institution) ? $samplearray->contributing_institution : '';
                        $creator = isset($samplearray->creator->data) ? $samplearray->creator->data : array();
                        $date = isset($samplearray->date->data) ? $samplearray->date->data : array();
                        $publisher = isset($samplearray->publisher->data) ? $samplearray->publisher->data : array();
                        $location = isset($samplearray->location->data) ? $samplearray->location->data : array();
                        $subject = isset($samplearray->subject->data) ? $samplearray->subject->data : array();
                        
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
                                                        <tr><th>Creator</th><td><?php foreach( $creator as $singlecreator) { echo $singlecreator."<br/>"; };?></td></tr>
                                                        <!--<tr><th>Created Date</th><td><?php foreach( $date as $singledate) { echo $singledate."<br/>"; };?></td></tr>-->
                                                        <tr><th>Created Date</th><td><?php echo $postprocdate;?></td></tr>
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
                                                <?php foreach($location as $georow) { 
                                                    
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
                                                    
                                                    //echo $bingapiurl;
                                                    //print_r($bingapiarray);
                                                    
                                                    
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
                                                        <a href="<?php echo $bingapiurl;?>"><?php echo $bingapiurl;?></a>
                                                    </td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <p><em class="text-muted">* These values are used by <a href="http://dp.la/info/developers/codex/">DPLA API projects</a> and <a href="http://dp.la/map">DPLA's "Explore by Map" interface</a>.</em></p> 

                                </div>

                              
                </div>
            </div>
          <?php } ?>
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