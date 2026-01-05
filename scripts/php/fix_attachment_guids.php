<?php
/**
 * Fix Attachment GUIDs
 * Corrige los GUIDs de attachments que apuntan a URLs de producto en vez de URLs de archivo
 */

require_once('/home/viveroloscocos.com.ar/public_html/wp-load.php');

echo "=== FIX ATTACHMENT GUIDS ===\n\n";

// Find attachments with wrong GUIDs (those that don't point to upload directory)
$query = "
    SELECT ID, post_title, guid, post_mime_type 
    FROM wp_posts 
    WHERE post_type = 'attachment' 
    AND post_mime_type LIKE 'image/%'
    AND guid NOT LIKE '%wp-content/uploads%'
    ORDER BY ID DESC
    LIMIT 500
";

global $wpdb;
$attachments = $wpdb->get_results($query);

echo "Attachments con GUIDs incorrectos: " . count($attachments) . "\n\n";

if (empty($attachments)) {
    echo "✅ No hay attachments para corregir!\n";
    exit(0);
}

$fixed = 0;
$failed = 0;

foreach ($attachments as $attachment) {
    $att_id = $attachment->ID;
    
    // Get the actual file path
    $file_path = get_post_meta($att_id, '_wp_attached_file', true);
    
    if (!$file_path) {
        echo "❌ Attachment $att_id: No tiene _wp_attached_file\n";
        $failed++;
        continue;
    }
    
    // Build correct GUID
    $upload_dir = wp_upload_dir();
    $correct_guid = $upload_dir['baseurl'] . '/' . $file_path;
    
    // Update GUID
    $result = $wpdb->update(
        'wp_posts',
        array('guid' => $correct_guid),
        array('ID' => $att_id),
        array('%s'),
        array('%d')
    );
    
    if ($result !== false) {
        echo "✅ Attachment $att_id: $correct_guid\n";
        $fixed++;
    } else {
        echo "❌ Attachment $att_id: Error al actualizar\n";
        $failed++;
    }
}

echo "\n=== RESUMEN ===\n";
echo "✅ Corregidos: $fixed\n";
echo "❌ Fallidos:   $failed\n";
echo "\n✅ Proceso completado!\n";
