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
        include('config.php');
        $identifier = isset($_GET['identifier']) ? $_GET['identifier'] : '';
        $set = isset($_GET['set']);
        
        if(empty($identifier)||empty($set)){
            echo "Required information not provided.";
        } else {
   
        function beautify($xmlString){

            // XML beautify function
            // http://lamehacks.net/blog/beautify-format-xml-with-php/

            $outputString = "";
            $previousBitIsCloseTag = false;
            $indentLevel = 0;
            $bits = explode("<", $xmlString);

            foreach($bits as $bit){

             $bit = trim($bit);
             if (!empty($bit)){

              if ($bit[0]=="/"){ $isCloseTag = true; }
              else{ $isCloseTag = false; }

              if(strstr($bit, "/>")){
               $prefix = "\n".str_repeat(" ",$indentLevel);
               $previousBitIsSimplifiedTag = true;
              }
              else{
               if ( !$previousBitIsCloseTag and $isCloseTag){
                if ($previousBitIsSimplifiedTag){
                 $indentLevel--;
                 $prefix = "\n".str_repeat(" ",$indentLevel);

                }
                else{
                 $prefix = "";
                 $indentLevel--;
                }
               }
               if ( $previousBitIsCloseTag and !$isCloseTag){$prefix = "\n".str_repeat(" ",$indentLevel); $indentLevel++;}
               if ( $previousBitIsCloseTag and $isCloseTag){$indentLevel--;$prefix = "\n".str_repeat(" ",$indentLevel);}
               if ( !$previousBitIsCloseTag and !$isCloseTag){{$prefix = "\n".str_repeat(" ",$indentLevel); $indentLevel++;}}
               $previousBitIsSimplifiedTag = false;
              }

              $outputString .= $prefix."<".$bit;

              $previousBitIsCloseTag = $isCloseTag;
             }
            }
            return $outputString;
        }
                               
        ?>
        
        <div class='container-fluid'>
            <div class='row'>
                <div class='col-md-12'>
    
                    <?php
    
                    $mfurl = "$oaibaseurl?verb=ListMetadataFormats&identifier=$identifier";

                    // create curl resource
                    $ch = curl_init();

                    // set url
                    curl_setopt($ch, CURLOPT_URL, $mfurl);

                    //return the transfer as a string
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    // $output contains the output string
                    $mfoutput = curl_exec($ch);

                    // close curl resource to free up system resources
                    curl_close($ch);   

                    $mfarray = simplexml_load_string($mfoutput);
               
                    ?>
                    <h2>OAI Record Viewer</h2>
                    <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#<?php echo $metadataprefix;?>" data-toggle="tab"><?php echo $metadataprefix;?></a></li>
                          <?php
                          
                          foreach($mfarray->ListMetadataFormats as $mformat) {
                              
                              if($mformat->metadataFormat->metadataPrefix !== $metadataprefix) {
                                  $thisprefix = $mformat->metadataFormat->metadataPrefix;
                                  ?>
                                  <li><a href="#<?php echo $thisprefix ?>" data-toggle="tab"><?php echo $thisprefix ?></a></li>
                          <?php
                              }
                          }
                          ?> 
                        </ul>

                   
                        <!-- Tab panes -->
                        <div class="tab-content">
                          <div class="tab-pane active" id="#<?php echo $metadataprefix;?>">
                         <?php
                          

                            $recurl = "$oaibaseurl?verb=GetRecord&metadataPrefix=$metadataprefix&identifier=$identifier";
                           
                            // create curl resource
                            $ch = curl_init();

                            // set url
                            curl_setopt($ch, CURLOPT_URL, $recurl);

                            //return the transfer as a string
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                            // $output contains the output string
                            $recoutput = curl_exec($ch);

                            // close curl resource to free up system resources
                            curl_close($ch);   

   
                            print '<pre class="prettyprint">' . htmlspecialchars(beautify($recoutput)) .'</pre>';
                          ?>
          
                          </div>
                            
                            
                            <?php
                          
                          foreach($mfarray->ListMetadataFormats as $mformat) {
                              
                              if($mformat->metadataFormat->metadataPrefix !== $metadataprefix) {
                                  $thisprefix = $mformat->metadataFormat->metadataPrefix;
                                  ?>
                            
                            <div class="tab-pane" id="<?php echo $thisprefix ?>">
                               <?php
                                $recurlvar = "recurl_$thisprefix";
                                $recoutputvar = "recoutput_$thisprefix";
                                $$recurlvar = "$oaibaseurl?verb=GetRecord&metadataPrefix=$thisprefix&identifier=$identifier";
                                // create curl resource
                                $ch = curl_init();

                                // set url
                                curl_setopt($ch, CURLOPT_URL, $$recurlvar);

                                //return the transfer as a string
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                // $output contains the output string
                                $$recoutputvar = curl_exec($ch);

                                // close curl resource to free up system resources
                                curl_close($ch);   

                                print '<pre class="prettyprint">' . htmlspecialchars(beautify($$recoutputvar)) .'</pre>';
                              ?>
           
                          </div>
                            
                         <?php  } } ?>

                        </div>
                </div>
            </div>
        </div>
        
        <?php } ?>
              <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?lang=xml"></script>
        <style type="text/css">
            pre.prettyprint {
                padding: 0;
                border: none;
                background-color: #fff;
            }
        </style>
    </body>
</html>