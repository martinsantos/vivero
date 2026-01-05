<?php
/**
 * Email styles for WooCommerce emails.
 *
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// This is the main style block that will be inlined
?>
#template_header_image {
    text-align: center;
    padding: 32px 0;
}

#template_header_image img {
    max-width: 100%;
    height: auto;
    max-height: 150px;
}

#body_content {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 600px;
    margin: 0 auto;
}

#body_content table {
    width: 100%;
    border-collapse: collapse;
}

#body_content table td {
    padding: 12px;
    vertical-align: top;
    border: 1px solid #e5e7eb;
}

#body_content table th {
    text-align: left;
    padding: 12px;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
}

#body_content h1,
#body_content h2,
#body_content h3,
#body_content h4 {
    color: #111827;
    margin-top: 0;
    margin-bottom: 16px;
}

#body_content h1 {
    font-size: 24px;
    font-weight: 700;
}

#body_content h2 {
    font-size: 20px;
    font-weight: 600;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 8px;
    margin-bottom: 16px;
}

#body_content p {
    margin: 0 0 16px;
    color: #4b5563;
    line-height: 1.5;
}

a {
    color: #059669;
    text-decoration: none;
}

a:hover {
    color: #047857;
    text-decoration: underline;
}

.wc-button,
.button {
    display: inline-block;
    background-color: #059669;
    color: #ffffff !important;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    text-align: center;
    margin: 16px 0;
}

.wc-button:hover,
.button:hover {
    background-color: #047857;
    text-decoration: none;
}

.order_item td {
    border-bottom: 1px solid #e5e7eb;
}

.order_item:last-child td {
    border-bottom: none;
}

.address {
    font-style: normal;
    margin-bottom: 16px;
}

.footer {
    margin-top: 32px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
    color: #6b7280;
    font-size: 14px;
    text-align: center;
}

/* Responsive styles */
@media only screen and (max-width: 600px) {
    #body_content {
        padding: 24px;
    }
    
    #body_content h1 {
        font-size: 20px;
    }
    
    #body_content h2 {
        font-size: 18px;
    }
    
    #body_content table,
    #body_content tbody,
    #body_content th,
    #body_content td,
    #body_content tr {
        display: block;
    }
    
    #body_content thead {
        display: none;
    }
    
    #body_content tr {
        margin-bottom: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }
    
    #body_content td {
        border: none;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
        padding-left: 50%;
        text-align: right;
    }
    
    #body_content td:before {
        position: absolute;
        left: 12px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        text-align: left;
        font-weight: 600;
        content: attr(data-title) ": ";
    }
    
    #body_content td:last-child {
        border-bottom: none;
    }
}
