<?php

require_once conf::i()->rootdir . '/core/library/sphinx/sphinxapi.php';

class sphinx
{
    private static $client;
    
    static function search($query, $index)
    {
        if (!self::$client)
        {
            self::$client = new SphinxClient ();
            self::$client->SetServer ( 'localhost', conf::i()->sphinx['port'] );
            self::$client->SetConnectTimeout( 1 );
            self::$client->SetWeights( array ( 100, 1 ) );
            self::$client->SetMatchMode( SPH_MATCH_ALL );
            self::$client->SetRankingMode( SPH_RANK_PROXIMITY_BM25 );
            self::$client->SetArrayResult( true );
        }
        
        return self::$client->Query( $query, $index );
    }
}

?>
