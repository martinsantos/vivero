<?php
/**
 * Product Infographic Custom Fields
 * 
 * Sistema de campos personalizados con iconos para productos de WooCommerce
 * Muestra infografía contextual según la categoría del producto
 *
 * @package Los_Cocos_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Definición de campos por categoría
 */
function loscocos_get_infographic_fields() {
    return array(
        // Campos para PLANTAS
        'plantas' => array(
            'luminosidad' => array(
                'label' => 'Luminosidad',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'sol_directo' => 'Sol Directo',
                    'luz_parcial' => 'Luz Parcial / Semisombra',
                    'sombra' => 'Sombra / Interior'
                ),
                'icon' => 'sun',
                'descriptions' => array(
                    'sol_directo' => 'Necesita exposición directa al sol',
                    'luz_parcial' => 'Sol directo o sombra parcial',
                    'sombra' => 'Prefiere lugares con poca luz'
                )
            ),
            'riego' => array(
                'label' => 'Riego',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'escaso' => 'Escaso',
                    'moderado' => 'Moderado',
                    'intenso' => 'Intenso'
                ),
                'icon' => 'water',
                'descriptions' => array(
                    'escaso' => 'Riego cada 10-15 días',
                    'moderado' => 'Moderado, 2-3 veces por semana',
                    'intenso' => 'Riego frecuente, mantener húmedo'
                )
            ),
            'nivel_cuidado' => array(
                'label' => 'Nivel de cuidado',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'facil' => 'Fácil',
                    'moderado' => 'Moderado',
                    'dificil' => 'Difícil'
                ),
                'icon' => 'leaf',
                'descriptions' => array(
                    'facil' => 'Fácil - Ideal para principiantes',
                    'moderado' => 'Moderado - Algo de experiencia',
                    'dificil' => 'Difícil - Requiere atención experta'
                )
            )
        ),
        
        // Campos para FERTILIZANTES
        'fertilizantes' => array(
            'tipo_fertilizante' => array(
                'label' => 'Tipo de Producto',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'fertilizante' => 'Fertilizante',
                    'enraizante' => 'Enraizante',
                    'bioestimulante' => 'Bioestimulante'
                ),
                'icon' => 'seedling',
                'descriptions' => array(
                    'fertilizante' => 'Aporta nutrientes esenciales',
                    'enraizante' => 'Estimula el desarrollo radicular',
                    'bioestimulante' => 'Mejora la vitalidad general'
                )
            ),
            'aplicacion' => array(
                'label' => 'Modo de Aplicación',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'foliar' => 'Foliar',
                    'riego' => 'Por Riego',
                    'suelo' => 'Aplicación al Suelo'
                ),
                'icon' => 'spray',
                'descriptions' => array(
                    'foliar' => 'Pulverizar sobre las hojas',
                    'riego' => 'Diluir en agua de riego',
                    'suelo' => 'Aplicar directamente al sustrato'
                )
            ),
            'frecuencia_fertilizante' => array(
                'label' => 'Frecuencia',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'semanal' => 'Semanal',
                    'quincenal' => 'Quincenal',
                    'mensual' => 'Mensual'
                ),
                'icon' => 'calendar',
                'descriptions' => array(
                    'semanal' => 'Aplicar cada 7 días',
                    'quincenal' => 'Aplicar cada 15 días',
                    'mensual' => 'Aplicar una vez al mes'
                )
            )
        ),
        // Campos para PLAGUICIDAS (insecticidas, funguicidas, herbicidas, molusquicidas)
        'plaguicidas' => array(
            'tipo_accion' => array(
                'label' => 'Clasificación',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'insecticida' => 'Insecticida',
                    'acaricida' => 'Acaricida',
                    'funguicida' => 'Funguicida',
                    'herbicida' => 'Herbicida',
                    'molusquicida' => 'Molusquicida',
                    'hormiguicida' => 'Hormiguicida'
                ),
                'icon' => 'shield',
                'descriptions' => array(
                    'insecticida' => 'Controla insectos plaga',
                    'acaricida' => 'Controla ácaros y arañuelas',
                    'funguicida' => 'Controla hongos y enfermedades',
                    'herbicida' => 'Elimina malezas',
                    'molusquicida' => 'Controla caracoles y babosas',
                    'hormiguicida' => 'Controla hormigas'
                )
            ),
            'plagas_objetivo' => array(
                'label' => 'Plagas que controla',
                'type' => 'multicheck',
                'options' => array(
                    'pulgones' => 'Pulgones',
                    'cochinillas' => 'Cochinillas',
                    'trips' => 'Trips',
                    'mosca_blanca' => 'Mosca Blanca',
                    'acaros' => 'Ácaros / Arañuela',
                    'orugas' => 'Orugas',
                    'minadores' => 'Minadores',
                    'hormigas' => 'Hormigas',
                    'hongos' => 'Hongos',
                    'oidio' => 'Oídio',
                    'roya' => 'Roya',
                    'malezas_hoja_ancha' => 'Malezas Hoja Ancha',
                    'malezas_gramineas' => 'Gramíneas',
                    'caracoles' => 'Caracoles / Babosas'
                ),
                'icon' => 'bug'
            ),
            'dosis_media' => array(
                'label' => 'Dosis Media',
                'type' => 'text',
                'icon' => 'beaker',
                'placeholder' => 'Ej: 3ml / litro de agua'
            ),
            'modo_uso' => array(
                'label' => 'Modo de Uso',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'pulverizacion' => 'Pulverización Foliar',
                    'riego' => 'Dilución en Riego',
                    'polvo' => 'Aplicación en Polvo',
                    'cebo' => 'Cebo / Granulado'
                ),
                'icon' => 'spray',
                'descriptions' => array(
                    'pulverizacion' => 'Pulverizar sobre las plantas',
                    'riego' => 'Diluir en agua de riego',
                    'polvo' => 'Espolvorear sobre las plantas',
                    'cebo' => 'Colocar cebos en el suelo'
                )
            ),
            'formulacion' => array(
                'label' => 'Formulación',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'concentrado_emulsionable' => 'Concentrado Emulsionable',
                    'polvo_soluble' => 'Polvo Soluble',
                    'polvo_mojable' => 'Polvo Mojable',
                    'granulado' => 'Granulado',
                    'liquido' => 'Líquido'
                ),
                'icon' => 'flask',
                'descriptions' => array(
                    'concentrado_emulsionable' => 'Concentrado emulsionable',
                    'polvo_soluble' => 'Polvo soluble en agua',
                    'polvo_mojable' => 'Polvo mojable',
                    'granulado' => 'Granulado listo para usar',
                    'liquido' => 'Líquido listo para usar'
                )
            )
        ),
        // Campos para MACETAS
        'macetas' => array(
            'material_maceta' => array(
                'label' => 'Material',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'plastico' => 'Plástico Premium',
                    'ceramica' => 'Cerámica Artesanal',
                    'fibrocemento' => 'Fibrocemento',
                    'barro' => 'Barro Cocido'
                ),
                'icon' => 'shield', // Usando shield como representativo de material/protección
                'descriptions' => array(
                    'plastico' => 'Plástico de alta resistencia UV',
                    'ceramica' => 'Cerámica con acabado brillante',
                    'fibrocemento' => 'Ideal para exteriores por su peso',
                    'barro' => 'Material poroso que respira'
                ),
                'default' => 'plastico'
            ),
            'forma_maceta' => array(
                'label' => 'Forma',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'redonda' => 'Redonda',
                    'cuadrada' => 'Cuadrada',
                    'piramidal' => 'Piramidal',
                    'bowl' => 'Bowl / Cuenco'
                ),
                'icon' => 'leaf', // Usando leaf como fallback visual
                'descriptions' => array(
                    'redonda' => 'Diseño clásico circular',
                    'cuadrada' => 'Líneas rectas y modernas',
                    'piramidal' => 'Base ancha y boca estrecha',
                    'bowl' => 'Baja y ancha para arreglos'
                ),
                'default' => 'redonda'
            ),
            'drenaje' => array(
                'label' => 'Drenaje',
                'type' => 'select',
                'options' => array(
                    '' => '-- Seleccionar --',
                    'con_agujero' => 'Con Drenaje',
                    'sin_agujero' => 'Sin Drenaje / Portamaceta'
                ),
                'icon' => 'water',
                'descriptions' => array(
                    'con_agujero' => 'Agujero de drenaje incluido',
                    'sin_agujero' => 'Ideal como base decorativa'
                ),
                'default' => 'con_agujero'
            )
        )
    );
}

/**
 * Determinar qué tipo de campos mostrar según la categoría del producto
 */
function loscocos_get_product_field_type($product_id) {
    $terms = get_the_terms($product_id, 'product_cat');
    
    if (!$terms || is_wp_error($terms)) {
        return 'plantas'; // Por defecto
    }
    
    $category_slugs = wp_list_pluck($terms, 'slug');
    
    // Mapeo de categorías a tipos de campos
    $plaguicida_cats = array('insecticidas', 'funguicidas', 'herbicidas', 'molusquicidas', 'acaricidas');
    $fertilizante_cats = array('fertilizantes', 'enraizantes', 'bioestimulantes');
    $maceta_cats = array('macetas', 'maceta', 'jarrones', 'fuentes');
    
    foreach ($category_slugs as $slug) {
        if (in_array($slug, $plaguicida_cats)) {
            return 'plaguicidas';
        }
        if (in_array($slug, $fertilizante_cats)) {
            return 'fertilizantes';
        }
        if (in_array($slug, $maceta_cats)) {
            return 'macetas';
        }
    }
    
    // Si es Uncategorized o no matcheó, buscar palabras clave en el título
    $product_title = get_the_title($product_id);
    if (stripos($product_title, 'maceta') !== false) {
        return 'macetas';
    }
    
    return 'plantas';
}

/**
 * Registrar Meta Box en el admin de productos
 */
function loscocos_add_infographic_meta_box() {
    add_meta_box(
        'loscocos_infographic_fields',
        '📊 Infografía del Producto',
        'loscocos_render_infographic_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'loscocos_add_infographic_meta_box');

/**
 * Renderizar el Meta Box
 */
function loscocos_render_infographic_meta_box($post) {
    wp_nonce_field('loscocos_infographic_save', 'loscocos_infographic_nonce');
    
    $field_type = loscocos_get_product_field_type($post->ID);
    $all_fields = loscocos_get_infographic_fields();
    $fields = $all_fields[$field_type];
    
    echo '<div class="loscocos-infographic-admin">';
    echo '<p class="description" style="margin-bottom: 15px; padding: 10px; background: #f0f0f1; border-radius: 4px;">';
    echo '<strong>Tipo detectado:</strong> ' . ucfirst($field_type) . ' - Complete los campos que desee mostrar en la ficha del producto.';
    echo '</p>';
    
    echo '<table class="form-table">';
    
    foreach ($fields as $field_key => $field) {
        $meta_key = '_loscocos_' . $field_key;
        $value = get_post_meta($post->ID, $meta_key, true);
        
        echo '<tr>';
        echo '<th><label for="' . esc_attr($meta_key) . '">' . esc_html($field['label']) . '</label></th>';
        echo '<td>';
        
        if ($field['type'] === 'select') {
            echo '<select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" style="width: 100%; max-width: 400px;">';
            foreach ($field['options'] as $opt_value => $opt_label) {
                $selected = selected($value, $opt_value, false);
                echo '<option value="' . esc_attr($opt_value) . '"' . $selected . '>' . esc_html($opt_label) . '</option>';
            }
            echo '</select>';
        } elseif ($field['type'] === 'multicheck') {
            $saved_values = is_array($value) ? $value : array();
            echo '<div style="display: flex; flex-wrap: wrap; gap: 8px; max-width: 600px;">';
            foreach ($field['options'] as $opt_value => $opt_label) {
                $checked = in_array($opt_value, $saved_values) ? 'checked' : '';
                echo '<label style="display: flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0f0f1; border-radius: 4px; cursor: pointer;">';
                echo '<input type="checkbox" name="' . esc_attr($meta_key) . '[]" value="' . esc_attr($opt_value) . '" ' . $checked . '>';
                echo esc_html($opt_label);
                echo '</label>';
            }
            echo '</div>';
        } else {
            $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
            echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" style="width: 100%; max-width: 400px;">';
        }
        
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</div>';
}

/**
 * Guardar los campos
 */
function loscocos_save_infographic_fields($post_id) {
    // Verificar nonce
    if (!isset($_POST['loscocos_infographic_nonce']) || !wp_verify_nonce($_POST['loscocos_infographic_nonce'], 'loscocos_infographic_save')) {
        return;
    }
    
    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Guardar todos los campos posibles
    $all_fields = loscocos_get_infographic_fields();
    
    foreach ($all_fields as $type => $fields) {
        foreach ($fields as $field_key => $field) {
            $meta_key = '_loscocos_' . $field_key;
            if (isset($_POST[$meta_key])) {
                if ($field['type'] === 'multicheck') {
                    $value = array_map('sanitize_text_field', (array) $_POST[$meta_key]);
                } else {
                    $value = sanitize_text_field($_POST[$meta_key]);
                }
                update_post_meta($post_id, $meta_key, $value);
            } elseif ($field['type'] === 'multicheck') {
                // Si no hay checkboxes seleccionados, guardar array vacío
                update_post_meta($post_id, $meta_key, array());
            }
        }
    }
}
add_action('save_post_product', 'loscocos_save_infographic_fields');

/**
 * Obtener iconos SVG
 */
function loscocos_get_infographic_icon($icon_name) {
    $icons = array(
        'sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>',
        'water' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>',
        'leaf' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path></svg>',
        'seedling' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 20h10"></path><path d="M10 20c5.5-2.5.8-6.4 3-10"></path><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"></path><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"></path></svg>',
        'spray' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v16a2 2 0 0 0 2 2h6"></path><path d="M3 10h6"></path><path d="M6 3v7"></path><path d="m20 8-5 5"></path><path d="m20 13-5-5"></path><path d="M17 16h4"></path><path d="M17 20h4"></path><path d="M21 16v4"></path></svg>',
        'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
        'shield' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
        'bug' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="14" x="8" y="6" rx="4"></rect><path d="m19 7-3 2"></path><path d="m5 7 3 2"></path><path d="m19 19-3-2"></path><path d="m5 19 3-2"></path><path d="M20 13h-4"></path><path d="M4 13h4"></path><path d="m10 4 1 2"></path><path d="m14 4-1 2"></path></svg>',
        'beaker' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 3h15"></path><path d="M6 3v16a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V3"></path><path d="M6 14h12"></path></svg>',
        'flask' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"></path><path d="M14 2v7.31"></path><path d="M12 9.31V14"></path><path d="M4.93 15.72L2.59 20.66a1 1 0 0 0 .9 1.44h16.02a1 1 0 0 0 .9-1.44l-2.34-4.94"></path><path d="M4.93 15.72A14.66 14.66 0 0 1 12 14c2.87 0 5.55.65 7.07 1.72"></path></svg>',
        'repeat' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4"></path><path d="M3 11v-1a4 4 0 0 1 4-4h14"></path><path d="m7 22-4-4 4-4"></path><path d="M21 13v1a4 4 0 0 1-4 4H3"></path></svg>'
    );
    
    return isset($icons[$icon_name]) ? $icons[$icon_name] : '';
}

/**
 * Renderizar la infografía en el frontend
 */
function loscocos_render_product_infographic() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $product_id = $product->get_id();
    $field_type = loscocos_get_product_field_type($product_id);
    $all_fields = loscocos_get_infographic_fields();
    $fields = $all_fields[$field_type];
    
    $has_content = false;
    $output = '<div class="loscocos-product-infographic">';
    
    foreach ($fields as $field_key => $field) {
        $meta_key = '_loscocos_' . $field_key;
        $value = get_post_meta($product_id, $meta_key, true);
        
        if (empty($value)) {
            // Usar default si existe para esta categoría
            if (isset($field['default'])) {
                $value = $field['default'];
            } elseif ($field_type === 'plantas') {
                // Hardcoded defaults for general plants
                if ($field_key === 'luminosidad') $value = 'sol_directo';
                if ($field_key === 'riego') $value = 'moderado';
                if ($field_key === 'nivel_cuidado') $value = 'facil';
            } else {
                continue;
            }
        }
        
        $has_content = true;
        
        // Manejar diferentes tipos de campos
        $description = '';
        
        if ($field['type'] === 'multicheck' && is_array($value)) {
            // Convertir array de slugs a nombres legibles
            $labels = array();
            foreach ($value as $slug) {
                if (isset($field['options'][$slug])) {
                    $labels[] = $field['options'][$slug];
                }
            }
            $description = implode(', ', $labels);
        } elseif ($field['type'] === 'select') {
            // Obtener descripción si existe, sino el label de la opción
            if (isset($field['descriptions'][$value])) {
                $description = $field['descriptions'][$value];
            } elseif (isset($field['options'][$value])) {
                $description = $field['options'][$value];
            } else {
                $description = $value;
            }
        } else {
            // Text field
            $description = $value;
        }
        
        if (empty($description)) {
            continue;
        }
        
        $icon = loscocos_get_infographic_icon($field['icon']);
        
        $output .= '<div class="infographic-item">';
        $output .= '<div class="infographic-icon">' . $icon . '</div>';
        $output .= '<div class="infographic-content">';
        $output .= '<span class="infographic-title">' . esc_html($field['label']) . '</span>';
        $output .= '<span class="infographic-value">' . esc_html($description) . '</span>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    if ($has_content) {
        echo $output;
    }
}

// Hook para mostrar la infografía después del precio
add_action('woocommerce_single_product_summary', 'loscocos_render_product_infographic', 25);
