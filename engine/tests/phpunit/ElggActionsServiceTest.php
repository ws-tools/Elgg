<?php
// @codeCoverageIgnoreStart
$engine = dirname(dirname(dirname(__FILE__)));
// require_once "$engine/lib/configuration.php";
require_once "$engine/lib/actions.php";
// @codeCoverageIgnoreEnd

class ElggActionsServiceTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Tests register, exists and unregisrer
	 */
	public function testRegister() {
		$actions = new Elgg_ActionsService();
		
		$this->assertFalse($actions->exists('test/output'));
		$this->assertFalse($actions->exists('test/not_registered'));

		$this->assertTrue($actions->register('test/output', dirname(__FILE__) . '/test_files/actions/output.php', 'public'));
		$this->assertTrue($actions->register('test/non_ex_file', dirname(__FILE__) . '/test_files/actions/non_existing_file.php', 'public'));
		
		$this->assertTrue($actions->exists('test/output'));
		$this->assertFalse($actions->exists('test/non_ex_file'));
		$this->assertFalse($actions->exists('test/not_registered'));
		
		return $actions;
	}
	
	/**
	 * @depends testRegister
	 */
	public function testUnregister($actions) {

		$this->assertTrue($actions->unregister('test/output'));
		$this->assertTrue($actions->unregister('test/non_ex_file'));
		$this->assertFalse($actions->unregister('test/not_registered'));
	
		$this->assertFalse($actions->exists('test/output'));
		$this->assertFalse($actions->exists('test/non_ex_file'));
		$this->assertFalse($actions->exists('test/not_registered'));
	}
	
	/**
	 * Tests overwriting existing action
	 */
	public function testOverwrite() {
		$actions = new Elgg_ActionsService();
		
		$this->assertFalse($actions->exists('test/output'));
		
		$this->assertTrue($actions->register('test/output', dirname(__FILE__) . '/test_files/actions/output.php', 'public'));
		
		$this->assertTrue($actions->exists('test/output'));
		
		$this->assertTrue($actions->register('test/output', dirname(__FILE__) . '/test_files/actions/output2.php', 'public'));
		
		$this->assertTrue($actions->exists('test/output'));
	}
	
	public function testActionsAccess() {
		$actions = new Elgg_ActionsService();
		
		$this->assertFalse($actions->exists('test/output'));
		$this->assertFalse($actions->exists('test/not_registered'));

		$this->assertTrue($actions->register('test/output', dirname(__FILE__) . '/test_files/actions/output.php', 'public'));
		$this->assertTrue($actions->register('test/output_logged_in', dirname(__FILE__) . '/test_files/actions/output.php', 'logged_in'));
		$this->assertTrue($actions->register('test/output_admin', dirname(__FILE__) . '/test_files/actions/output.php', 'admin'));
		
		//TODO finish this test
		$this->markTestIncomplete("Can't test registration due to missing configuration.php dependencies");
// 		$actions->execute('test/not_registered');
	}
	
	//TODO call non existing

	
	//TODO token generation/validation
// 	public function testGenerateValidateTokens() {
// 		$actions = new Elgg_ActionsService();
		
// 		$i = 40;
		
// 		while ($i-->0) {
// 			$timestamp = rand(100000000, 2000000000);
// 			$token = $actions->generateActionToken($timestamp);
// 			$this->assertTrue($actions->validateActionToken(false, $token, $timestamp));
// 			$this->assertFalse($actions->validateActionToken(false, $token, $timestamp+1));
// 			$this->assertFalse($actions->validateActionToken(false, $token, $timestamp-1));
// 		}
		
// 	}
	
	//TODO gatekeeper?
}
