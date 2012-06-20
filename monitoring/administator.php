<?php
/*
 * credits to Scotty http://stackoverflow.com/questions/2752431/any-way-to-access-gearman-administration
*/
class Waps_Gearman_Server {

    /**
     * @var string
     */
    protected $host = "127.0.0.1";
    /**
     * @var int
     */
    protected $port = 4730;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host=null,$port=null){
        if( !is_null($host) ){
            $this->host = $host;
        }
        if( !is_null($port) ){
            $this->port = $port;
        }
    }

    /**
     * @return array | null
     */
    public function getStatus(){
        $status = null;
        $handle = fsockopen($this->host,$this->port,$errorNumber,$errorString,30);
        if($handle!=null){
            fwrite($handle,"status\n");
            while (!feof($handle)) {
                $line = fgets($handle, 4096);
                if( $line==".\n"){
                    break;
                }
                if( preg_match("~^(.*)[ \t](\d+)[ \t](\d+)[ \t](\d+)~",$line,$matches) ){
                    $function = $matches[1];
                    $status['operations'][$function] = array(
                        'function' => $function,
                        'total' => $matches[2],
                        'running' => $matches[3],
                        'connectedWorkers' => $matches[4],
                    );
                }
            }
            fwrite($handle,"workers\n");
            while (!feof($handle)) {
                $line = fgets($handle, 4096);
                if( $line==".\n"){
                    break;
                }
                // FD IP-ADDRESS CLIENT-ID : FUNCTION
                if( preg_match("~^(\d+)[ \t](.*?)[ \t](.*?) : ?(.*)~",$line,$matches) ){
                    $fd = $matches[1];
                    $status['connections'][$fd] = array(
                        'fd' => $fd,
                        'ip' => $matches[2],
                        'id' => $matches[3],
                        'function' => $matches[4],
                    );
                }
            }
            fclose($handle);
        }

        return $status;
    }
}

$server = new Waps_Gearman_Server();
var_dump($server->getStatus());