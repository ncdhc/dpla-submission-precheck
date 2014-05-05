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
        $identifier = isset($_GET['identifier']);
        $set = isset($_GET['set']);
        
        if(empty($identifier)||empty($set)){
            echo "Required information not provided.";
        } else {
            
    
        ?>
        
        <div class='container-fluid'>
            <div class='row'>
                <div class='col-md-12'>
                    <h3 class="text-muted"><em><?php echo $provider; ?></em></h3>
                    <h1>DPLA Submission Pre&middot;Check <span class='small'>OAI Record Viewer</span></h1>
                    <br>
                    
       
                    <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#<?php echo $metadataprefix;?>" data-toggle="tab"><?php echo $metadataprefix;?></a></li>
                          <li><a href="#oai_dc" data-toggle="tab">Original Format (oai_dc)</a></li>
                         
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                          <div class="tab-pane active" id="#<?php echo $metadataprefix;?>">
                          <?php
                          /*
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
                            
                            echo $recoutput;
                           */
                          
                          ?>
                          Testing    
                          </div>
                          <div class="tab-pane" id="oai_dc">
                               <?php
                            /* $recurl2 = "$oaibaseurl?verb=GetRecord&metadataPrefix=oai_dc&identifier=$identifier";
                            // create curl resource
                            $ch = curl_init();

                            // set url
                            curl_setopt($ch, CURLOPT_URL, $recurl2);

                            //return the transfer as a string
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                            // $output contains the output string
                            $recoutput2 = curl_exec($ch);

                            // close curl resource to free up system resources
                            curl_close($ch);   
                            
                            echo $recoutput2;
                            */
                          ?>
                              Testing
                          </div>
                      
                        </div>
                </div>
            </div>
        </div>
        
        <?php } ?>
              <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>