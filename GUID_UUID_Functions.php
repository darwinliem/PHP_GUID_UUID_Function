<?php
class UUID {
    // Helper function to check if a given UUID is valid
    private function is_valid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

    public function generate($version=4,$namespace=null,$name=null){
        switch ($version){
            case 1 :
                return $this->generateUUIDv1();
                break;
            case 3 :
                if(!empty($namespace) && !empty($name)){
                    return $this->generateUUIDv3($namespace, $name);
                } else {
                    return false;
                }
                break;
            default :
            case 2 :
            case 4 :
                return $this->generateUUIDv4();
                break;
            case 5 :
                if(!empty($namespace) && !empty($name)){
                    return $this->generateUUIDv5($namespace, $name);
                } else {
                    return false;
                }
                break;
            case 6 :
                if(!empty($namespace) && !empty($name)){
                    return $this->generateUUIDv6($namespace, $name);
                } else {
                    return false;
                }
                break;
            }

    }
    
    public function generateUUIDv1() {
        // Get the current time in 100-nanosecond intervals since 1582-10-15 00:00:00
        $time = sprintf('%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), (microtime(true) + 0x01B21DD213814000) * 10
        );
        // Get a random clock sequence
        $clockSeq = sprintf('%02x%02x', mt_rand(0, 0xff), mt_rand(0, 0xff));
        // Get a random node ID
        $node = sprintf('%02x%02x%02x%02x%02x%02x',
            mt_rand(0, 0xff), mt_rand(0, 0xff),
            mt_rand(0, 0xff), mt_rand(0, 0xff),
            mt_rand(0, 0xff), mt_rand(0, 0xff)
        );
        // Assemble the UUID
        return sprintf('%08s-%04s-%04s-%02s%02s-%012s',
            $time,
            substr($time, 0, 4),
            substr($time, 4, 4),
            $clockSeq,
            $node
        );
    }

    public function generateUUIDv3($namespace, $name) {
        if(!self::is_valid($namespace)) return false;
        $nstr = str_replace(array('-','{','}'), '', $namespace);
        $hash = md5($nstr . $name);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }



    public function generateUUIDv4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function generateUUIDv5($namespace, $name) {
        if(!self::is_valid($namespace)) return false;
        $nstr = str_replace(array('-','{','}'), '', $namespace);
        $hash = sha1($nstr . $name);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    public function generateUUIDv6($namespace, $name) {
        if(!self::is_valid($namespace)) return false;
        $nstr = str_replace(array('-','{','}'), '', $namespace);
        $hash = hash('sha256', $nstr . $name);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x6000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }
}
?>