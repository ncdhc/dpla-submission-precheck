<?php

// DPLA Submission Checker Configuration
        $provider = "North Carolina Digital Heritage Center"; // DPLA Service Hub Name
        $oaibaseurl = "http://brevard.lib.unc.edu:8080/repox/OAIHandler"; // DPLA Service Hub Base URL
        $metadataprefix = "MODS";
        $bingapikey = "Aqy8w3t1Ec04SwOsLX_B2AMWv6zT0G8m-3r0PFhbufXJ-tbdD7DWUcbSYycXC3s7";
        $harvestday = "19"; // Day of the month DPLA is scheduled to harvest
        $helpcontact = "Lisa Gregory";
        $helpemail = "gregoryl@email.unc.edu";
        $helpphone = "(919) 962-4839";
        
        $dataprovider = isset($_GET['dataprovider']) ? $_GET['dataprovider'] : '';
?>