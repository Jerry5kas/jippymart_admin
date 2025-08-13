<?php
/**
 * Comprehensive Test Script for CMS Pages, On-boarding Screens, and Email Templates Activity Logging
 * 
 * This script tests the activity logging implementation for all three modules:
 * - CMS Pages: Create, update, delete, publish/unpublish operations
 * - On-boarding Screens: Update operations
 * - Email Templates: Create, update, delete operations
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ActivityLoggingTest
{
    private $activityLogger;
    private $client;
    private $baseUrl = 'http://127.0.0.1:8000';

    public function __construct()
    {
        $this->activityLogger = new ActivityLogger();
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false
        ]);
    }

    public function runAllTests()
    {
        echo "🚀 Starting Comprehensive Activity Logging Tests for CMS Pages, On-boarding Screens, and Email Templates\n";
        echo "================================================================================\n\n";

        $this->testActivityLoggerService();
        $this->testDirectFirestoreLogging();
        $this->testApiEndpoint();
        $this->testBladeFileImplementations();

        echo "\n✅ All tests completed!\n";
    }

    private function testActivityLoggerService()
    {
        echo "📋 Testing ActivityLogger Service...\n";
        
        try {
            // Create a mock user
            $user = new \stdClass();
            $user->id = 'test_user_123';
            $user->name = 'Test User';

            // Create a mock request
            $request = new Request();
            $request->merge([
                'ip' => '127.0.0.1',
                'user_agent' => 'Test Browser'
            ]);

            // Test logging for each module
            $modules = [
                'cms_pages' => 'Created new CMS page: Test Page',
                'on_boarding' => 'Updated on-boarding screen: Test Screen',
                'email_templates' => 'Created new email template: Test Template'
            ];

            foreach ($modules as $module => $description) {
                $result = $this->activityLogger->log($user, $module, 'test_action', $description, $request);
                if ($result) {
                    echo "  ✅ ActivityLogger service test passed for {$module}\n";
                } else {
                    echo "  ❌ ActivityLogger service test failed for {$module}\n";
                }
            }

        } catch (\Exception $e) {
            echo "  ❌ ActivityLogger service test failed: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    private function testDirectFirestoreLogging()
    {
        echo "🔥 Testing Direct Firestore Logging...\n";
        
        try {
            $firestore = app('firebase.firestore');
            $collection = $firestore->collection('activity_logs');

            // Test data for each module
            $testData = [
                [
                    'user_id' => 'test_user_123',
                    'user_type' => 'admin',
                    'role' => 'super_admin',
                    'module' => 'cms_pages',
                    'action' => 'created',
                    'description' => 'Created new CMS page: Test Page',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Browser',
                    'created_at' => new \Google\Cloud\Core\Timestamp(new \DateTime())
                ],
                [
                    'user_id' => 'test_user_123',
                    'user_type' => 'admin',
                    'role' => 'super_admin',
                    'module' => 'on_boarding',
                    'action' => 'updated',
                    'description' => 'Updated on-boarding screen: Test Screen',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Browser',
                    'created_at' => new \Google\Cloud\Core\Timestamp(new \DateTime())
                ],
                [
                    'user_id' => 'test_user_123',
                    'user_type' => 'admin',
                    'role' => 'super_admin',
                    'module' => 'email_templates',
                    'action' => 'created',
                    'description' => 'Created new email template: Test Template',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Browser',
                    'created_at' => new \Google\Cloud\Core\Timestamp(new \DateTime())
                ]
            ];

            foreach ($testData as $data) {
                $collection->add($data);
                echo "  ✅ Direct Firestore logging test passed for {$data['module']}\n";
            }

        } catch (\Exception $e) {
            echo "  ❌ Direct Firestore logging test failed: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    private function testApiEndpoint()
    {
        echo "🌐 Testing API Endpoint...\n";
        
        try {
            $testData = [
                [
                    'module' => 'cms_pages',
                    'action' => 'created',
                    'description' => 'Created new CMS page: Test Page'
                ],
                [
                    'module' => 'on_boarding',
                    'action' => 'updated',
                    'description' => 'Updated on-boarding screen: Test Screen'
                ],
                [
                    'module' => 'email_templates',
                    'action' => 'created',
                    'description' => 'Created new email template: Test Template'
                ]
            ];

            foreach ($testData as $data) {
                $response = $this->client->post($this->baseUrl . '/api/activity-logs/log', [
                    'form_params' => $data,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ]
                ]);

                $statusCode = $response->getStatusCode();
                $responseData = json_decode($response->getBody(), true);

                if ($statusCode === 200 && isset($responseData['success']) && $responseData['success']) {
                    echo "  ✅ API endpoint test passed for {$data['module']} (Status: {$statusCode})\n";
                } else {
                    echo "  ❌ API endpoint test failed for {$data['module']} (Status: {$statusCode})\n";
                    echo "     Response: " . json_encode($responseData) . "\n";
                }
            }

        } catch (RequestException $e) {
            echo "  ❌ API endpoint test failed: " . $e->getMessage() . "\n";
            if ($e->hasResponse()) {
                echo "     Status Code: " . $e->getResponse()->getStatusCode() . "\n";
                echo "     Response: " . $e->getResponse()->getBody() . "\n";
            }
        }
        echo "\n";
    }

    private function testBladeFileImplementations()
    {
        echo "📄 Testing Blade File Implementations...\n";
        
        $bladeFiles = [
            'cms_pages' => [
                'resources/views/cms/create.blade.php',
                'resources/views/cms/edit.blade.php',
                'resources/views/cms/index.blade.php'
            ],
            'on_boarding' => [
                'resources/views/on-board/save.blade.php'
            ],
            'email_templates' => [
                'resources/views/email_templates/save.blade.php',
                'resources/views/email_templates/index.blade.php'
            ]
        ];

        foreach ($bladeFiles as $module => $files) {
            echo "  📁 Testing {$module} module:\n";
            
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    
                    // Check for logActivity calls
                    if (strpos($content, 'logActivity') !== false) {
                        echo "    ✅ {$file} - logActivity calls found\n";
                        
                        // Count logActivity calls
                        $count = substr_count($content, 'logActivity');
                        echo "       Found {$count} logActivity call(s)\n";
                        
                        // Check for await logActivity
                        if (strpos($content, 'await logActivity') !== false) {
                            echo "       ✅ Properly awaited logActivity calls\n";
                        } else {
                            echo "       ⚠️  Some logActivity calls may not be awaited\n";
                        }
                        
                        // Check for specific module logging
                        if (strpos($content, "'{$module}'") !== false || strpos($content, "\"{$module}\"") !== false) {
                            echo "       ✅ Module '{$module}' properly referenced\n";
                        } else {
                            echo "       ⚠️  Module '{$module}' not found in logActivity calls\n";
                        }
                    } else {
                        echo "    ❌ {$file} - No logActivity calls found\n";
                    }
                } else {
                    echo "    ❌ {$file} - File not found\n";
                }
            }
            echo "\n";
        }
    }
}

// Run the tests
try {
    $test = new ActivityLoggingTest();
    $test->runAllTests();
} catch (Exception $e) {
    echo "❌ Test execution failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
