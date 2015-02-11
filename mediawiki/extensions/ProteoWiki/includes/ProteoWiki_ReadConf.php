<?php

/** Here a set of reading configuration functions **/


class ProteoWikiReadConf {

    /** read Configuration and return value */
    public static function readConf( $class="" ) {
        
        $output = array();

        global $wgProteoWikiPages;
        
        if ( empty( $class ) ) {
            
            $output["status"] = "Error";
            $output["msg"] = "No defined class";
            
            return $output;
        }
        
        $params = array();
        
        foreach ( $wgProteoWikiPages as $key => $confPages ) {
            
            foreach ( $confPages as $confPage ) {

                $title = Title::newFromText( $confPage, NS_PROTEOWIKICONF );
                $params[ $key ] = ProteoWikiImport::listFromPageConf( $title );

            }
        }
        
        
        return $params;
        
    }
    
}