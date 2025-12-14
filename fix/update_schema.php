<?php
/**
 * Enhanced Schema Update & Validation Script
 * 
 * This script:
 * - Checks all required fields in all tables
 * - Creates missing fields automatically
 * - Displays before/after structure
 * - Provides detailed success messages
 * 
 * Safe to run multiple times - idempotent design
 * 
 * Usage:
 *   Browser: http://localhost:8000/fix/update_schema.php
 *   CLI: php fix/update_schema.php
 *   Production: https://aiquiz.vibeai.cv/fix/update_schema.php
 */

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/api/config.php';
require_once $projectRoot . '/api/db.php';

$isCLI = (php_sapi_name() === 'cli');

if (!$isCLI) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html><html><head><title>Schema Validation & Update</title>";
    echo "<style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 1000px; margin: 30px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #2c3e50; border-bottom: 3px solid #667eea; padding-bottom: 15px; margin-bottom: 25px; }
        h2 { color: #34495e; margin-top: 30px; border-left: 4px solid #667eea; padding-left: 15px; }
        .success { color: #27ae60; background: #d5f4e6; padding: 12px; border-left: 4px solid #27ae60; margin: 12px 0; border-radius: 4px; }
        .info { color: #2980b9; background: #d6eaf8; padding: 12px; border-left: 4px solid #2980b9; margin: 12px 0; border-radius: 4px; }
        .warning { color: #e67e22; background: #fdebd0; padding: 12px; border-left: 4px solid #e67e22; margin: 12px 0; border-radius: 4px; }
        .error { color: #c0392b; background: #fadbd8; padding: 12px; border-left: 4px solid #c0392b; margin: 12px 0; border-radius: 4px; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #667eea; }
        .step-title { font-weight: bold; color: #2c3e50; font-size: 1.1em; margin-bottom: 10px; }
        pre { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 6px; overflow-x: auto; font-size: 0.9em; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #667eea; color: white; padding: 12px; text-align: left; font-weight: 600; }
        td { padding: 10px 12px; border-bottom: 1px solid #ecf0f1; }
        tr:hover { background: #f8f9fa; }
        .summary { background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%); padding: 25px; border-radius: 8px; margin-top: 30px; border-left: 5px solid #27ae60; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.85em; font-weight: bold; margin: 0 5px; }
        .badge-new { background: #27ae60; color: white; }
        .badge-existing { background: #95a5a6; color: white; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; margin: 10px 0; }
        .stat-label { font-size: 0.9em; opacity: 0.9; }
    </style></head><body><div class='container'>";
}

function output($message, $type = 'info') {
    global $isCLI;
    if ($isCLI) {
        $prefix = match($type) {
            'success' => '✓',
            'error' => '✗',
            'warning' => '⚠',
            'info' => 'ℹ',
            default => '•'
        };
        echo "$prefix $message\n";
    } else {
        echo "<div class='$type'>$message</div>";
    }
}

function outputStep($step, $message) {
    global $isCLI;
    if ($isCLI) {
        echo "\n[$step] $message\n";
    } else {
        echo "<div class='step'><div class='step-title'>[$step]</div>$message</div>";
    }
}

// Track all changes
$allChanges = [];
$tablesChecked = 0;
$fieldsChecked = 0;
$fieldsAdded = 0;
$indexesAdded = 0;

// Define required schema
$requiredSchema = [
    'questions' => [
        'is_demo' => [
            'type' => 'TINYINT(1)',
            'default' => '0',
            'after' => 'ai_prompt',
            'description' => 'Flag for demo questions'
        ]
    ],
    // Can add more tables and fields here as needed
];

try {
    if (!$isCLI) {
        echo "<h1>🔧 Database Schema Validation & Update</h1>";
        echo "<div class='info'><strong>Environment:</strong> " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'development') . " | ";
        echo "<strong>Database:</strong> " . DB_NAME . " | ";
        echo "<strong>Time:</strong> " . date('Y-m-d H:i:s') . "</div>";
    } else {
        echo "========================================\n";
        echo "  Database Schema Validation & Update\n";
        echo "========================================\n";
        echo "Database: " . DB_NAME . "\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n\n";
    }

    // ============================================
    // STEP 1: Validate and Update Each Table
    // ============================================
    
    foreach ($requiredSchema as $tableName => $requiredFields) {
        $tablesChecked++;
        outputStep("TABLE: $tableName", "Checking table structure");
        
        // Get current table structure BEFORE changes
        $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
        $currentColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $currentColumnNames = array_column($currentColumns, 'Field');
        
        if (!$isCLI) {
            echo "<h2>📋 Table: $tableName</h2>";
            echo "<p>Current columns: " . count($currentColumns) . "</p>";
        }

        $tableChanges = [];
        
        // Check each required field
        foreach ($requiredFields as $fieldName => $fieldConfig) {
            $fieldsChecked++;
            
            if (!in_array($fieldName, $currentColumnNames)) {
                // Field missing - add it
                output("Field '$fieldName' not found. Adding...", 'warning');
                
                $sql = "ALTER TABLE $tableName ADD COLUMN $fieldName {$fieldConfig['type']} DEFAULT {$fieldConfig['default']}";
                if (isset($fieldConfig['after'])) {
                    $sql .= " AFTER {$fieldConfig['after']}";
                }
                
                $pdo->exec($sql);
                $fieldsAdded++;
                
                $change = "Added field '$fieldName' to table '$tableName'";
                $tableChanges[] = $change;
                $allChanges[] = $change;
                
                output("✓ Successfully added field '$fieldName'", 'success');
            } else {
                output("Field '$fieldName' already exists", 'success');
            }
        }
        
        // Get UPDATED table structure AFTER changes
        $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
        $updatedColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Display structure comparison
        if (!$isCLI) {
            echo "<h3>Table Structure:</h3>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Status</th></tr>";
            
            foreach ($updatedColumns as $column) {
                $isNew = !in_array($column['Field'], $currentColumnNames);
                $badge = $isNew ? "<span class='badge badge-new'>NEW</span>" : "<span class='badge badge-existing'>OK</span>";
                
                echo "<tr>";
                echo "<td><strong>{$column['Field']}</strong></td>";
                echo "<td>{$column['Type']}</td>";
                echo "<td>{$column['Null']}</td>";
                echo "<td>{$column['Key']}</td>";
                echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
                echo "<td>$badge</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            if (!empty($tableChanges)) {
                echo "<div class='success'><strong>Changes made to $tableName:</strong><ul>";
                foreach ($tableChanges as $change) {
                    echo "<li>$change</li>";
                }
                echo "</ul></div>";
            } else {
                echo "<div class='info'>No changes needed for table '$tableName'</div>";
            }
        }
    }

    // ============================================
    // STEP 2: Add Indexes for Performance
    // ============================================
    outputStep("INDEXES", "Checking performance indexes");
    
    $requiredIndexes = [
        'questions' => [
            'idx_is_demo' => ['columns' => ['is_demo'], 'description' => 'Demo questions filter']
        ]
    ];
    
    foreach ($requiredIndexes as $tableName => $indexes) {
        foreach ($indexes as $indexName => $indexConfig) {
            $stmt = $pdo->query("SHOW INDEX FROM $tableName WHERE Key_name = '$indexName'");
            $indexExists = $stmt->fetch();
            
            if (!$indexExists) {
                output("Adding index '$indexName' on $tableName", 'info');
                
                $columns = implode(', ', $indexConfig['columns']);
                $pdo->exec("ALTER TABLE $tableName ADD INDEX $indexName ($columns)");
                
                $indexesAdded++;
                $change = "Added index '$indexName' on $tableName ({$indexConfig['description']})";
                $allChanges[] = $change;
                
                output("✓ Index '$indexName' added successfully", 'success');
            } else {
                output("Index '$indexName' already exists on $tableName", 'success');
            }
        }
    }

    // ============================================
    // STEP 3: Verify Database Integrity
    // ============================================
    outputStep("INTEGRITY CHECK", "Verifying database integrity");
    
    // Check foreign keys
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME, 
            CONSTRAINT_NAME, 
            REFERENCED_TABLE_NAME,
            COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE 
            TABLE_SCHEMA = '" . DB_NAME . "' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    output("Found " . count($foreignKeys) . " foreign key constraints", 'success');
    
    // Check all required tables exist
    $requiredTables = ['users', 'sessions', 'profiles', 'media_library', 'questions', 'quiz_attempts', 'quiz_responses', 'user_activity_logs'];
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $missingTables = array_diff($requiredTables, $existingTables);
    
    if (empty($missingTables)) {
        output("All " . count($requiredTables) . " required tables exist", 'success');
    } else {
        output("⚠ Missing tables: " . implode(', ', $missingTables), 'error');
    }
    
    // ============================================
    // STEP 4: Test Database Connection
    // ============================================
    outputStep("CONNECTION TEST", "Testing database queries");
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
    $questionCount = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions WHERE is_demo = 1");
    $demoCount = $stmt->fetch()['count'];
    
    output("Database queries successful", 'success');
    
    // ============================================
    // FINAL SUMMARY
    // ============================================
    
    if (!$isCLI) {
        echo "<div class='summary'>";
        echo "<h2>✅ Schema Update Complete!</h2>";
        
        // Statistics
        echo "<div class='stats'>";
        echo "<div class='stat-box'><div class='stat-number'>$tablesChecked</div><div class='stat-label'>Tables Checked</div></div>";
        echo "<div class='stat-box'><div class='stat-number'>$fieldsChecked</div><div class='stat-label'>Fields Validated</div></div>";
        echo "<div class='stat-box'><div class='stat-number'>$fieldsAdded</div><div class='stat-label'>Fields Added</div></div>";
        echo "<div class='stat-box'><div class='stat-number'>$indexesAdded</div><div class='stat-label'>Indexes Created</div></div>";
        echo "</div>";
        
        if (!empty($allChanges)) {
            echo "<h3>📝 Changes Applied:</h3>";
            echo "<div class='success'><ul>";
            foreach ($allChanges as $change) {
                echo "<li><strong>$change</strong></li>";
            }
            echo "</ul></div>";
        } else {
            echo "<div class='info'><h3>✓ No Changes Needed</h3>";
            echo "<p>Your database schema is already up to date!</p></div>";
        }
        
        echo "<h3>📊 Database Statistics:</h3>";
        echo "<ul>";
        echo "<li><strong>Total Questions:</strong> $questionCount</li>";
        echo "<li><strong>Demo Questions:</strong> $demoCount</li>";
        echo "<li><strong>Foreign Keys:</strong> " . count($foreignKeys) . "</li>";
        echo "<li><strong>Tables:</strong> " . count($existingTables) . "</li>";
        echo "</ul>";
        
        echo "<h3>✅ Success!</h3>";
        echo "<div class='success'>";
        echo "<strong>Your database schema has been successfully validated and updated!</strong><br><br>";
        echo "All required fields exist and indexes are optimized for performance.";
        echo "</div>";
        
        echo "<hr style='margin: 30px 0;'>";
        echo "<h3>📋 Next Steps:</h3>";
        echo "<ol>";
        echo "<li>✓ Verify your application is working correctly</li>";
        echo "<li>✓ Test the demo quiz feature</li>";
        echo "<li>✓ Check admin panel for questions management</li>";
        echo "<li><strong style='color: #c0392b;'>⚠ IMPORTANT: Delete this file (fix/update_schema.php) for security!</strong></li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "\n========================================\n";
        echo "  ✅ Schema Update Complete!\n";
        echo "========================================\n\n";
        
        echo "Statistics:\n";
        echo "  Tables Checked: $tablesChecked\n";
        echo "  Fields Validated: $fieldsChecked\n";
        echo "  Fields Added: $fieldsAdded\n";
        echo "  Indexes Created: $indexesAdded\n\n";
        
        if (!empty($allChanges)) {
            echo "Changes Applied:\n";
            foreach ($allChanges as $change) {
                echo "  • $change\n";
            }
        } else {
            echo "✓ No changes needed. Schema is up to date!\n";
        }
        
        echo "\nDatabase Statistics:\n";
        echo "  Total Questions: $questionCount\n";
        echo "  Demo Questions: $demoCount\n";
        echo "  Foreign Keys: " . count($foreignKeys) . "\n";
        echo "  Tables: " . count($existingTables) . "\n";
        
        echo "\n✅ SUCCESS! Database schema validated and updated.\n";
        echo "\nNext Steps:\n";
        echo "  1. Verify application is working\n";
        echo "  2. Test demo quiz feature\n";
        echo "  3. DELETE this file (fix/update_schema.php)\n\n";
    }

} catch (PDOException $e) {
    output("\n❌ Database Error: " . $e->getMessage(), 'error');
    if (!$isCLI) {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo $e->getTraceAsString() . "\n";
    }
    exit(1);
} catch (Exception $e) {
    output("\n❌ Error: " . $e->getMessage(), 'error');
    if (!$isCLI) {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo $e->getTraceAsString() . "\n";
    }
    exit(1);
}

if (!$isCLI) {
    echo "</div></body></html>";
}
?>
