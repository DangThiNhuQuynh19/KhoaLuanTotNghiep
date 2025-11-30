<?php
/**
 * Simple Test Script for Messaging Feature
 * 
 * Usage: php tests/test_messaging.php
 * 
 * This script tests the basic functionality of the messaging system:
 * 1. Environment configuration loading
 * 2. Database connection
 * 3. Message saving
 * 4. Message retrieval
 * 5. New message polling
 */

// Change to project root
chdir(__DIR__ . '/..');

echo "=== Messaging Feature Test Suite ===\n\n";

$tests_passed = 0;
$tests_failed = 0;

function test($name, $condition, $message = '') {
    global $tests_passed, $tests_failed;
    if ($condition) {
        echo "✅ PASS: {$name}\n";
        $tests_passed++;
    } else {
        echo "❌ FAIL: {$name}" . ($message ? " - {$message}" : "") . "\n";
        $tests_failed++;
    }
}

// Test 1: Environment configuration
echo "--- Test 1: Environment Configuration ---\n";
require_once('env.php');

test('env() function exists', function_exists('env'));
test('config() function exists', function_exists('config'));
test('getWebSocketUrl() function exists', function_exists('getWebSocketUrl'));
test('isWebSocketEnabled() function exists', function_exists('isWebSocketEnabled'));
test('getUploadDir() function exists', function_exists('getUploadDir'));
test('getUploadUrl() function exists', function_exists('getUploadUrl'));

// Test 2: Configuration values
echo "\n--- Test 2: Configuration Values ---\n";
$dbHost = config('database.host');
$dbDatabase = config('database.database');
$wsEnabled = config('websocket.enabled');
$pollingInterval = config('polling.interval');

test('Database host is configured', !empty($dbHost), "Value: {$dbHost}");
test('Database name is configured', !empty($dbDatabase), "Value: {$dbDatabase}");
test('WebSocket enabled is boolean', is_bool($wsEnabled), "Value: " . var_export($wsEnabled, true));
test('Polling interval is positive integer', is_numeric($pollingInterval) && $pollingInterval > 0, "Value: {$pollingInterval}");

// Test 3: Database connection
echo "\n--- Test 3: Database Connection ---\n";
require_once('Models/ketnoi.php');

$db = new clsketnoi();
$connection = $db->moKetNoi();

test('Database connection established', $connection !== null && $connection !== false);

if ($connection) {
    // Test 4: ChatUserModel
    echo "\n--- Test 4: ChatUserModel ---\n";
    require_once('Models/ChatUserModel.php');
    
    $chat = new ChatUserModel();
    
    test('ChatUserModel instantiated', $chat instanceof ChatUserModel);
    
    // Test message save
    $testSender = 'test_sender_' . time();
    $testReceiver = 'test_receiver_' . time();
    $testMessage = 'Test message ' . date('Y-m-d H:i:s');
    
    $chat->setSender($testSender);
    $chat->setReceiver($testReceiver);
    $chat->setMessage($testMessage);
    $messageId = $chat->saveMessage();
    
    test('Message saved successfully', $messageId !== false && $messageId > 0, "Message ID: {$messageId}");
    
    // Test message retrieval
    $messages = $chat->getMessages($testSender, $testReceiver);
    
    test('Messages retrieved successfully', is_array($messages));
    test('Retrieved messages contain our test message', count($messages) > 0);
    
    if (count($messages) > 0) {
        $lastMessage = end($messages);
        test('Last message sender matches', $lastMessage['sender'] === $testSender);
        test('Last message content matches', $lastMessage['message'] === $testMessage);
    }
    
    // Test new messages (polling)
    $newMessages = $chat->getNewMessages($testSender, $testReceiver, date('Y-m-d H:i:s', strtotime('-1 hour')));
    
    test('getNewMessages returns array', is_array($newMessages));
    test('getNewMessages returns recent messages', count($newMessages) > 0);
    
    // Cleanup test data
    $stmt = $connection->prepare("DELETE FROM tinnhan WHERE tentk_gui = ?");
    $stmt->bind_param("s", $testSender);
    $stmt->execute();
    $stmt->close();
    echo "\n[Cleanup] Test messages deleted\n";
    
    $db->dongKetNoi($connection);
}

// Test 5: Configuration helper functions
echo "\n--- Test 5: Helper Functions ---\n";
$wsUrl = getWebSocketUrl();
$uploadDir = getUploadDir();
$uploadUrl = getUploadUrl('test.pdf');

test('WebSocket URL generated', !empty($wsUrl), "Value: {$wsUrl}");
test('Upload directory configured', !empty($uploadDir), "Value: {$uploadDir}");
test('Upload URL includes filename', strpos($uploadUrl, 'test.pdf') !== false, "Value: {$uploadUrl}");

// Summary
echo "\n=== Test Summary ===\n";
echo "Passed: {$tests_passed}\n";
echo "Failed: {$tests_failed}\n";
echo "Total:  " . ($tests_passed + $tests_failed) . "\n";

if ($tests_failed > 0) {
    echo "\n⚠️  Some tests failed. Please check the configuration.\n";
    exit(1);
} else {
    echo "\n✅ All tests passed!\n";
    exit(0);
}
