<?php
// plug/lang/*.menu.php

//Serf

//alerts
addS("serf_alert_installed", "Serf is installed");
addS("serf_alert_not_installed", "Serf is not installed");
addS("serf_alert_not_publishing", "Serf is not publishing local services");
addS("serf_alert_publishing", "Serf is publishing local services");
addS("serf_alert_running", "Serf is running");
addS("serf_alert_stopped", "Serf is stopped");
addS("serf_alert_will_publish", "Serf will publish local services");
addS("serf_alert_wont_publish", "Serf will not publish local services");

//buttons
addS("serf_button_install", "Install Serf");
addS("serf_button_publish", "Enable services publication via Serf");
addS("serf_button_unpublish", "Disable services publication via Serf");
addS("serf_button_save", "Save configuration");
addS("serf_button_start", "Start Serf");
addS("serf_button_stop", "Stop Serf");
addS("serf_button_uninstall", "Uninstall Serf");

//common
addS("serf_common_title", "Serf");
addS("serf_common_subtitle", "A Distributed Services Announcement and Discovery (DADS) tool");

//flash
addS("serf_flash_installed", "Serf has been installed");
addS("serf_flash_publishing", "Enabling services publication via Serf...");
addS("serf_flash_publishing_error", "Services publication via Serf was not enabled");
addS("serf_flash_saving", "Saving Serf configuration...");
addS("serf_flash_starting", "Starting Serf...");
addS("serf_flash_stopping", "Stopping Serf...");
addS("serf_flash_uninstalled", "Serf has been uninstalled");
addS("serf_flash_unpublishing", "Disabling services publication via Serf...");
addS("serf_flash_unpublishing_error", "Services publication via Serf was not disabled");

//index
addS("serf_index_change_configuration", "Use this form to modify Serf configuration:");
addS("serf_index_current_configuration", "This is the current Serf configuration. To change it, stop Serf first.");
addS("serf_index_description_1", "<a href='https://serfdom.io/' target='_blank'>Serf</a> is a decentralized, lightweight and highly available solution for managing cluster membership, failure detection and service orchestration. It uses an efficient and lightweight gossip protocol to communicate between nodes. Serf is completely masterless with no single point of failure.");
addS("serf_index_description_2", "The Distributed Announcement and Discovery of Services (DADS) for Community Networks included in Cloudy uses Serf to exchange communication between nodes. If services publication is enabled, local services will be announced to the network and other users will be available to see and use them.");
addS("serf_index_form_bootstrap", "Bootstrap node");
addS("serf_index_form_bootstrap_tooltip", "IP address and port of a running Serf node. It will be used to bootstrap the service. Default is 10.139.40.22:5000");
addS("serf_index_form_rpc", "RPC address");
addS("serf_index_form_rpc_tooltip", "IP address and port for the remote procedure call (RPC). Default is 127.0.0.1:7373");
addS("serf_index_form_bind", "Bind port");
addS("serf_index_form_bind_tooltip", "The port on which Serf will listen for messages from other nodes. Default is 5000");
addS("serf_index_subtitle_configuration", "Configuration");
addS("serf_index_publication", "Services publication:");
addS("serf_index_status", "Serf status:");

//search
addS("serf_search_quality", "Scan quality of services");
