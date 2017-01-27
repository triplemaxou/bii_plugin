<?php
// ADD YOUR SHORTCODES HERE
function bii_current_year(){
	return date("Y");
}
add_shortcode("current-year", "bii_current_year");

function getFilesOfFolder($atts) {
    //var_dump($attr);
    $atts = shortcode_atts(
        array(
            'folder' => false
        ), $atts, 'filesOfFolder' );
    
    //check if folder id is an integer
    if (intval($atts['folder'])) {
        
        $structure = RML_Structure::newInstance();
        $folder = $structure->getFolderByID($atts['folder']);
        //check if folder exist
        if (wp_rml_get_by_id($atts['folder'])) {
            
            $fileIDs = RML_Folder::sFetchFileIds($atts['folder']);
            //pre($fileIDs);
            
            if (count($fileIDs) > 0) {
                //$query = new WP_Query( array( 'post__in' => $fileIDs ) );
                $files = get_posts(array( 'post__in' => $fileIDs , 'post_type' => 'attachment', 'numberposts' => -1));
                //pre($files);
                
                $nbrFiles = count($files);
                $i = 0;
                
                $contents = "<div class='vc_row'>";
                foreach ($files as $file) {
                    $i++;
                    if ($i % 6 == 0) {
                        $contents .= "</div><div class='vc_row'>";
                    }
                    
                    $contents .= "<div class='vc_col-md-2'>";
                    
                    $contents .= "<a href='".$file->guid."' >".$file->post_title." - ".$file->post_mime_type."</a>"; 
                    
                    $contents .= "</div>";
                }
                
                $contents .= "<div/>";
                return $contents;
                
            }
            return "<p>Aucun fichier<p/>";
        }
    }
    return "<p>Dossier inexistant</p>";
}
add_shortcode("filesOfFolder", "getFilesOfFolder");
