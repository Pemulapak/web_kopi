<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Titik Awal Kopitiam - Kafe Terbaik di Jatinangor</title>
    
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arial", sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: opacity 0.5s ease;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            font-size: 18px;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #8b4513;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-menu a:hover {
            color: #8b4513;
        }

        .nav-menu a::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #8b4513;
            transition: width 0.3s ease;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #8b4513;
            margin: 3px 0;
            transition: 0.3s;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                url("img/foto.jpg");
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }

        .hero-content {
            max-width: 800px;
            padding: 0 2rem;
            animation: fadeInUp 1s ease;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            background: #8b4513;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }

        .cta-button:hover {
            background: #a0522d;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.4);
        }

        /* Section Styles */
        .section {
            padding: 5rem 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #8b4513;
            margin-bottom: 1rem;
            position: relative;
        }

        .section-title::after {
            content: "";
            position: absolute;
            width: 60px;
            height: 3px;
            background: #8b4513;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }

        /* About Section */
        .about {
            background: #f9f9f9;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }

        .about-image {
            text-align: center;
        }

        .about-image img {
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Menu Section */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .menu-item {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-10px);
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .menu-item h3 {
            color: #8b4513;
            margin-bottom: 0.5rem;
        }

        .menu-item .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #8b4513;
            margin-top: 1rem;
        }

        /* Benefits Section */
        .benefits {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .benefit-item {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .benefit-item:hover {
            transform: translateY(-5px);
        }

        .benefit-item i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ffe4b5;
        }

        .benefit-item h3 {
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        /* Member Registration */
        .member-form {
            background: #f9f9f9;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8b4513;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }

        .strength-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
            color: #dc3545;
        }

        .strength-item.valid {
            color: #28a745;
        }

        .strength-item i {
            margin-right: 0.5rem;
            font-size: 0.7rem;
        }

        .password-match {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            display: none;
        }

        .password-match.show {
            display: block;
        }

        .password-match.valid {
            color: #28a745;
        }

        .password-match.invalid {
            color: #dc3545;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #8b4513;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #a0522d;
        }

        /* Footer */
        .footer {
            background: #2c1810;
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: #8b4513;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .footer-section p,
        .footer-section a {
            color: #ccc;
            text-decoration: none;
            line-height: 1.6;
        }

        .footer-section a:hover {
            color: #8b4513;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: #8b4513;
            color: white;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: #a0522d;
            transform: translateY(-3px);
        }

        .footer-map iframe {
            width: 100%;
            height: 300px;
            border: none;
            border-radius: 8px;
        }



        .footer-bottom {
            border-top: 1px solid #444;
            padding-top: 1rem;
            text-align: center;
            color: #888;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .modal-bg {
            background-color: rgba(0, 0, 0, 0.7);
        }

        .coffee-theme {
            background: linear-gradient(to right, #3e2f2f, #a47149);
            color: #fdf5e6;
        }

        .coffee-input {
            background-color: #f3e5ab;
            color: #3e2f2f;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background-color: white;
                width: 100%;
                text-align: center;
                transition: 0.3s;
                box-shadow: 0 10px 27px rgba(0, 0, 0, 0.05);
                padding: 2rem 0;
            }

            .nav-menu.active {
                left: 0;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .form-container {
                padding: 2rem 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-map iframe {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .nav-container {
                padding: 0 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>