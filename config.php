<?php

// DPLA Submission Checker Configuration
        $provider = "North Carolina Digital Heritage Center"; // DPLA Service Hub Name
        $oaibaseurl = "http://repox.lib.unc.edu:8080/repox/OAIHandler"; // DPLA Service Hub Base URL
        $metadataprefix = "MODS";  // If this changes, please edit stylesheets in /xsl accordingly
        $harvestday = "13"; // Day of the month DPLA is scheduled to harvest
        $helpcontact = "Lisa Gregory"; // Name of Help Contact Person
        $helpemail = "digitalnc@unc.edu"; // Email address for Help requests
        $helpphone = "(919) 962-4345"; // Phone number for Help requests
        
        // DO NOT EDIT
        $dataprovider = isset($_GET['dataprovider']) ? $_GET['dataprovider'] : '';
?>
