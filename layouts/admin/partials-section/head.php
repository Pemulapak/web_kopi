<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kelola Member</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.min.css">

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <script defer data-api="/stats/event" data-domain="preview.tabler.io" src="/stats/js/script.js"></script>
    <meta name="msapplication-TileColor" content="#066fd1" />
    <meta name="theme-color" content="#066fd1" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <link rel="icon" href="./favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
    <meta name="description"
        content="Tabler is packed with beautifully crafted components and powerful features. Jump in and start building a stunning dashboard — all for free!" />
    <meta name="canonical" content="https://preview.tabler.io/layout-fluid-vertical.html" />
    <meta name="twitter:image:src" content="https://preview.tabler.io/static/og.png" />
    <meta name="twitter:site" content="@tabler_ui" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title"
        content="Tabler: Premium and Open Source dashboard template with responsive and high quality UI." />
    <meta name="twitter:description"
        content="Tabler is packed with beautifully crafted components and powerful features. Jump in and start building a stunning dashboard — all for free!" />
    <meta property="og:image" content="https://preview.tabler.io/static/og.png" />
    <meta property="og:image:width" content="1280" />
    <meta property="og:image:height" content="640" />
    <meta property="og:site_name" content="Tabler" />
    <meta property="og:type" content="object" />
    <meta property="og:title"
        content="Tabler: Premium and Open Source dashboard template with responsive and high quality UI." />
    <meta property="og:url" content="https://preview.tabler.io/static/og.png" />
    <meta property="og:description"
        content="Tabler is packed with beautifully crafted components and powerful features. Jump in and start building a stunning dashboard — all for free!" />
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="./dist/libs/jsvectormap/dist/jsvectormap.css?1747690965" rel="stylesheet" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="./dist/css/tabler.min.css?1747690965" rel="stylesheet"
        integrity="sha384-7aLJEogmAYs7X+MSqgKyrK8EQgOyuqTPfYrAs0hJt6IOuVczGdjpiNMmHiXRozR5" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PLUGINS STYLES -->
    <link href="./dist/css/tabler-flags.min.css?1747690965" rel="stylesheet"
        integrity="sha384-H7p4Cgh2RM3r9apL63WdIpPu9BtIhbGngx0f3WrqyCpunfY+X7M4apXpQQcZqA5c" />
    <link href="./dist/css/tabler-socials.min.css?1747690965" rel="stylesheet"
        integrity="sha384-bujgRtkeNSwK8hFfCUO6SyIyWngJuWblefHIViyjq5XbDAEeCi1YRlIqggw9Vcqu" />
    <link href="./dist/css/tabler-payments.min.css?1747690965" rel="stylesheet"
        integrity="sha384-sCkGfjP5FkkIIL7H3FNIhYK1ZvUwMQeOLVSfOblaDQoKZX8k71BHZk1LMHMPRjbx" />
    <link href="./dist/css/tabler-vendors.min.css?1747690965" rel="stylesheet"
        integrity="sha384-yYQlhpXu43M9F6EsDV1V/GQBPy9gQSKTTkvRiSnKEbUjI3/RhmpARqgI9pUTU6Z9" />
    <link href="./dist/css/tabler-marketing.min.css?1747690965" rel="stylesheet"
        integrity="sha384-jdZv/iHkc5OuAPy1SD9QancYje5FHhs4s1GL7AOvKdb6ZMiC+Zwya8pCGDBb7tIf" />
    <link href="./dist/css/tabler-themes.min.css?1747690965" rel="stylesheet"
        integrity="sha384-EWgzD0j3PnZ9hhq/YFnSNV/Hm54BB0hWYSSCEj6OR1YxP0BVDEM7Enp+UAJDMqBx" />
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN DEMO STYLES -->
    <link href="./preview/css/demo.min.css?1747690965" rel="stylesheet"
        integrity="sha384-BUDq2P684xwRBf0GDlySvob+KJg4ko8y2K7njgvYBscmEuqoVVqJ75zcTDozwkFA" />
    <!-- END DEMO STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-right: 20px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .search-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-info {
            background: #3498db;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .btn-info:hover {
            background: #2980b9;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            padding: 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .table-header h2 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .pagination {
            padding: 25px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a,
        .pagination span {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a {
            background: #f8f9fa;
            color: #667eea;
            border: 1px solid #e1e5e9;
        }

        .pagination a:hover {
            background: #667eea;
            color: white;
        }

        .pagination .current {
            background: #667eea;
            color: white;
            border: 1px solid #667eea;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e1e5e9;
        }

        .close {
            font-size: 2rem;
            cursor: pointer;
            color: #999;
        }

        .close:hover {
            color: #333;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
        }

        .detail-value {
            color: #666;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .search-form {
                flex-direction: column;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>