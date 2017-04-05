<?php

// DPLA Submission Checker Configuration
        $provider = ""; // DPLA Service Hub Name
        $oaibaseurl = ""; // DPLA Service Hub Base URL
        $metadataprefix = "MODS";  // If this changes, please edit stylesheets in /xsl accordingly
        $harvestday = ""; // Day of the month DPLA is scheduled to harvest, e.g., "20"
        $helpcontact = ""; // Name of Help Contact Person
        $helpemail = ""; // Email address for Help requests
        $helpphone = ""; // Phone number for Help requests

        // DO NOT EDIT UNLESS CHANGING DATAPROVIDER REQUIREMENT
        $dataprovider = isset($_GET['dataprovider']) ? $_GET['dataprovider'] : '';
?>
