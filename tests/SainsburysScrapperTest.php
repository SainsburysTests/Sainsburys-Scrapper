<?php

/**
 * Description of SainsburysTest
 *
 * @author User
 */
class SainsburysScrapperTest extends PHPUnit_Framework_TestCase {
    
    public function testJSON(){
        $s = new SainsburysScrapper();
        $json = $s->fetch();
        $this->assertNotNull($json);
    }
    
}
