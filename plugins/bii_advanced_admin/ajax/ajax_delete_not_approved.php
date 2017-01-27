<?php
echo comments::delete_not_approved();
echo " not approved ";
echo commentmeta::delete_orphans();
echo " orphans";
