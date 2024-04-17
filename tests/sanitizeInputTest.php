<?php
use PHPUnit\Framework\TestCase;
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data,);
    $allowed_chars = "a-zA-Z0-9 .@äüöÄÜÖ_-";
    return preg_replace("/[^" . $allowed_chars . "]/", '', $data);
}
class sanitizeInputTest extends TestCase
{
    public function testSuccessSanitizeInput()
    {
        $input = "<script>alert('XSS')</script>";
        $expectedOutput = "ltscriptgtalert039XSS039ltscriptgt";
        $result = sanitizeInput($input);
        $this->assertEquals($expectedOutput, $result);
    }
    public function testSanitizeInputAllowsAllowedCharacters()
    {
        $input = "yannic.schuepbach@usb.ch";
        $expectedOutput = "yannic.schuepbach@usb.ch";
        $result = sanitizeInput($input);
        $this->assertEquals($expectedOutput, $result);
    }
}