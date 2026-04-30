<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizational Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: #f5f5f5;
            min-width: 320px;
        }

        .tree {
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 1600px;
            overflow-x: auto;
            padding: 10px;
            -webkit-overflow-scrolling: touch;
        }

        .tree ul {
            padding: 20px 0;
            position: relative;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .tree li {
            display: inline-block;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 5px 0 5px;
            vertical-align: top;
            min-width: min(200px, 90vw);
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Hide all child ULs by default */
        .tree ul ul {
            display: none;
            width: 100%;
            padding-top: 20px;
        }

        /* Show child UL when parent LI has .expanded class */
        .tree li.expanded > ul {
            display: flex;
        }

        /* Hide siblings when a node is active */
        .tree li.active ~ li {
            display: none;
        }

        .tree li::before,
        .tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            width: 50%;
            height: 20px;
            border-top: 2px solid #ccc;
        }

        .tree li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #ccc;
        }

        .tree li:only-child::before,
        .tree li:only-child::after {
            display: none;
        }

        .tree li:only-child {
            padding-top: 0;
        }

        .tree li:first-child::before,
        .tree li:last-child::after {
            border: none;
        }

        .tree li:last-child::before {
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
        }

        .tree li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            width: 0;
            height: 20px;
        }

        .tree li a {
            display: inline-block;
            border: 2px solid #3498db;
            padding: clamp(8px, 2vw, 12px) clamp(10px, 3vw, 20px);
            text-decoration: none;
            color: #2c3e50;
            font-weight: 600;
            border-radius: 8px;
            font-size: clamp(12px, 2.5vw, 14px);
            background-color: white;
            min-width: min(180px, 80vw);
            max-width: min(250px, 90vw);
            word-wrap: break-word;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 0 5px;
            white-space: normal;
            line-height: 1.4;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .tree li a:hover {
            background: #3498db;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .tree li a:active {
            transform: translateY(0);
        }

        .tree li a::after {
            content: '▼';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: clamp(10px, 2vw, 12px);
            opacity: 0.7;
        }

        .tree li.expanded > a::after {
            transform: translateY(-50%) rotate(180deg);
        }

        .company-logo {
            display: block;
            margin: 0 auto 20px;
            width: min(300px, 80vw);
            height: auto;
        }

        .search-box {
            display: flex;
            justify-content: center;
            margin: 10px auto;
            width: min(500px, 95vw);
            padding: 0 10px;
            box-sizing: border-box;
        }

        .search-box input {
            width: 100%;
            padding: clamp(8px, 2vw, 15px);
            border: 2px solid #3498db;
            border-radius: 4px;
            font-size: clamp(14px, 2.5vw, 16px);
        }

        .search-box input:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        /* Mobile-first breakpoints */
        @media (max-width: 480px) {
            body {
                padding: 5px;
            }

            .tree {
                padding: 5px;
                margin: 5px;
            }

            .tree li {
                padding: 15px 2px 0 2px;
            }

            .tree li::before,
            .tree li::after {
                height: 15px;
            }

            .tree ul ul::before {
                height: 15px;
            }
        }

        /* Tablet */
        @media (min-width: 481px) and (max-width: 768px) {
            .tree {
                padding: 15px;
            }

            .tree li {
                min-width: min(180px, 85vw);
            }
        }

        /* Laptop */
        @media (min-width: 769px) and (max-width: 1024px) {
            .tree li {
                min-width: min(190px, 85vw);
            }
        }

        /* Desktop */
        @media (min-width: 1025px) {
            .tree {
                padding: 20px;
            }

            .tree li {
                min-width: 200px;
            }
        }

        /* Print styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .tree {
                width: 100%;
                max-width: none;
                overflow: visible;
            }

            .search-box {
                display: none;
            }

            .tree li a {
                border: 1px solid #000;
                box-shadow: none;
            }

            .tree li::before,
            .tree li::after,
            .tree ul ul::before {
                border-color: #000;
            }
        }

        /* High contrast mode */
        @media (prefers-contrast: high) {
            .tree li a {
                border: 2px solid #000;
                color: #000;
                background: #fff;
            }

            .tree li::before,
            .tree li::after,
            .tree ul ul::before {
                border-color: #000;
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .tree li a {
                transition: none;
            }

            .tree li a:hover {
                transform: none;
            }
        }

        /* New feature styles */
        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 15px auto;
            flex-wrap: wrap;
            max-width: 1200px;
            padding: 0 10px;
        }

        .control-btn {
            padding: 8px 15px;
            border: 2px solid #3498db;
            background: white;
            color: #2c3e50;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            background: #3498db;
            color: white;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px auto;
            max-width: 1200px;
            padding: 10px;
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .breadcrumb span {
            color: #3498db;
            cursor: pointer;
        }

        .breadcrumb span:hover {
            text-decoration: underline;
        }

        .breadcrumb .separator {
            color: #95a5a6;
        }

        .employee-details {
            position: fixed;
            top: 50%;
            right: -400px;
            transform: translateY(-50%);
            width: 350px;
            background: white;
            padding: 20px;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1000;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 8px 0 0 8px;
        }

        .employee-details.active {
            right: 0;
        }

        .employee-details h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
        }

        .employee-details .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 20px;
            color: #95a5a6;
        }

        .detail-item {
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: bold;
            color: #7f8c8d;
            font-size: 0.9em;
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .zoom-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: #3498db;
            color: white;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .zoom-btn:hover {
            background: #2980b9;
        }

        .zoom-level {
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .employee-details {
                width: 100%;
                right: -100%;
            }

            .zoom-controls {
                bottom: 10px;
                right: 10px;
            }
        }

        /* Add notes styles */
        .notes-section {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .notes-section h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notes-list {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
        }

        .note-item {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 8px;
            position: relative;
            border-left: 3px solid #3498db;
        }

        .note-item .note-date {
            font-size: 0.8em;
            color: #95a5a6;
            margin-bottom: 5px;
        }

        .note-item .note-text {
            color: #2c3e50;
            word-break: break-word;
        }

        .note-item .note-actions {
            position: absolute;
            top: 8px;
            right: 8px;
            display: none;
        }

        .note-item:hover .note-actions {
            display: flex;
            gap: 5px;
        }

        .note-action-btn {
            background: none;
            border: none;
            color: #95a5a6;
            cursor: pointer;
            padding: 2px;
            font-size: 12px;
        }

        .note-action-btn:hover {
            color: #3498db;
        }

        .add-note-form {
            display: flex;
            gap: 8px;
        }

        .note-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            resize: vertical;
            min-height: 60px;
        }

        .note-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .add-note-btn {
            align-self: flex-start;
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-note-btn:hover {
            background: #2980b9;
        }

        .notes-empty {
            text-align: center;
            color: #95a5a6;
            padding: 20px 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="search-box">
        <input type="text" placeholder="Search employees..." id="searchInput">
    </div>
    
    <div class="controls">
        <button class="control-btn" id="exportPDF">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z"/>
                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
            </svg>
            Export PDF
        </button>
        <button class="control-btn" id="expandAll">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Expand All
        </button>
        <button class="control-btn" id="collapseAll">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
            </svg>
            Collapse All
        </button>
    </div>

    <div class="tree" id="orgChart">
        <img src="https://fidelisgroup.in/assets/imgs/logo/Logo_Fidelis.png" alt="Fidelis Technology" class="company-logo">
        <ul>
            <li class="expanded">
                <a href="javascript:void(0);">SUBRAHMANYA B A (CEO)</a>
                <ul>
                    <li>
                        <a href="javascript:void(0);">NAGASHREE KS (CFO)</a>
                        <ul>
                            <li><a href="#">SAMHITHA</a></li>
                            <li><a href="#">SUNEEL KUMAR KC</a></li>
                            <li><a href="#">NANDESH T</a></li>
                            <li>
                                <a href="javascript:void(0);">KAVITHA C (SENIOR EXECUTIVE - FINANCE & ACCOUNTS)</a>
                                <ul>
                                    <li><a href="#">SHRADDHA</a></li>
                                    <li><a href="#">AVIN K N</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">SHRIDHARA SUNDARARAJ (CTO)</a>
                        <ul>
                            <li><a href="#">PRASHANT V KOKANE (GM - DELIVERY (IT SERVICES))</a>
                                <ul>
                                    <li><a href="#">ARUN DANIEL PHILIP</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">NAIK CHETAN LAXMAN (SENIOR SOFTWARE ENGINEER)</a>
                                <ul>
                                    <li><a href="#">NANDA KUMAR</a></li>
                                    <li><a href="#">KARTEEK K R</a></li>
                                </ul>
                            </li>
                            <li><a href="#">RAKESH KUMAR SUTAR</a></li>
                            <li><a href="#">BASAVARAJ HALLI (OPEN SOURCE DEVELOPER)</a>
                                <ul>
                                    <li><a href="#">RAJESH NAYAK</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">SRIDHAR R (ODOO IMPLEMENTATION MANAGER)</a>
                                <ul>
                                    <li><a href="#">SABARINATHAN</a></li>
                                    <li><a href="#">ABDUL AMEER</a></li>
                                    <li><a href="#">PRABHUDEVA</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">RAGHAVENDRA MESTA (CHRO & HEAD OPERATIONS)</a>
                        <ul>
                            <li>
                                <a href="javascript:void(0);">SENTHIL J (MANAGER - PAYROLL & OPERATIONS)</a>
                                <ul>
                                    <li><a href="#">RUPESH</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">RAMYA K (ASSISTANT MANAGER - CORPORATE HIRING)</a>
                                <ul>
                                    <li><a href="#">MONICA</a></li>
                                    <li><a href="#">SHRUTI G</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">ROSHAN ALVA (GENERAL MANAGER - DELIVERY)</a>
                                <ul>
                                    <li><a href="#">K G ANISH</a></li>
                                    <li><a href="#">PRAKYATH MESTHA</a></li>
                                    <li><a href="#">JACKSON VINCENT MATHIAS</a></li>
                                    <li><a href="#">UDAY BOVI</a></li>
                                    <li>
                                        <a href="javascript:void(0);">CHANDAN PREMSAGAR TIWARI (ASSOCIATE ACCOUNT MANAGER)</a>
                                        <ul>
                                            <li><a href="#">SWAPNIL KADAM</a></li>
                                        </ul>
                                    <li>
                                        <a href="javascript:void(0);">PRASHANTH B (RECRUITMENT MANAGER)</a>
                                        <ul>
                                            <li><a href="#">PRADEEP D</a></li>
                                            <li><a href="#">MEGHANA P</a></li>
                                            <li><a href="#">KUNDANAM JAYA SHREE</a></li>
                                            <li><a href="#">GLINDA NIKITHA PAUL</a></li>
                                            <li><a href="#">NIKHITA C S</a></li>
                                            <li><a href="#">ROOPA R</a></li>
                                            <li><a href="#">SANTHOSH K</a></li>
                                            <li><a href="#">SURESH B RATHOD</a></li>
                                            <li><a href="#">VIDYASHREE B</a></li>
                                            <li><a href="#">S SONI PRIYA</a></li>
                                            <li><a href="#">SMITA PAL</a></li>
                                            <li><a href="#">SATHYA KARTHICK</a></li>
                                            <li><a href="#">BONDILI SIREESHA</a></li>
                                            <li><a href="#">POOJA AAKASH MANE</a></li>

                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">BHARATH K V (MANAGER COMPLIANCE)</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">PRASHANTH KUMAR SHETTY (ASST. MANAGER TALENT ACQUISITION (GENERAL STAFFING))</a>
                                <ul>
                                    <li><a href="#">SUJAY C</a></li>
                                    <li><a href="#">SANDESH J M</a></li>
                                    <li><a href="#">SRIGISH</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">UDAYA B M (SENIOR MANAGER - HR & OPERATIONS)</a>
                                <ul>
                                    <li><a href="#">SHWETHA M S RAO (ASSISTANT MANAGER - LEGAL AFFAIRS)</a></li>
                                    <li>
                                        <a href="javascript:void(0);">MALUPULA RAMANJANEYULU (ASSISANT MANAGER HR & OPERATIONS)</a>
                                        <ul>
                                            <li><a href="#">PRADEEP D</a></li>
                                            <li><a href="#">BRISHALI MUKHERJEE</a></li>
                                            <li><a href="#">V A ZAINAB FARHEEN</a></li>
                                            <li><a href="#">REDDY JWALA MANOHAR</a></li>
                                            <li><a href="#">RIA KUNDU</a></li>
                                            <li><a href="#">SANTOSH NAGESHA MESTHA</a></li>
                                            <li><a href="#">RAKSHITHA N ESWAR</a></li>
                                            <li>
                                                <a href="javascript:void(0);">AKSHATA GOPAL MESTHA (LEAD HR)</a>
                                                <ul>
                                                    <li><a href="#">CHETHANA N</a></li>
                                                </ul>
                                        </ul>

                                    </li>
                            </li>
                            <li>
                                <a href="javascript:void(0);">DEVAKUMAR M (HR - LEAD & OPERATIONS)</a>
                                <ul>
                                    <li><a href="#">ANUSHREE M S</a></li>

                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">KALPITHA Y (HRBP)</a>
                                <ul>
                                    <li><a href="#">JAYARANI G</a></li>

                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">SUNIL G (GENERAL MANAGER - PROJECTS)</a>
                        <ul>
                            <li>
                                <a href="javascript:void(0);">HIMAYAT</a>
                                <ul>
                                    <li><a href="#">NISAR AHMAD BHAT</a></li>
                                    <li><a href="#">AMIR AYOUB</a></li>
                                    <li><a href="#">TANVEER AHMAD WANI</a></li>

                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            
            <li>
                <a href="javascript:void(0);">CHANDRASEKAR G (COO)</a>
                <ul>
                    <li><a href="#">PRIYANKA M R</a></li>
                    <li><a href="#">PRADEEP S R</a></li>
                    <li><a href="#">RAJESH P</a></li>
                    <li><a href="#">GANESH A</a></li>
                    <li><a href="#">SANDEEP B K</a></li>
                    <li><a href="#">SAMRAT D N</a></li>
                    <li><a href="#">NILESH GOSWAMI</a></li>
                    <li><a href="#">MOHAMED NASIM IDRISI</a></li>
                    <li>
                        <a href="javascript:void(0);">SANDHYASHREE (BUSINESS DEVELOPMENT MANAGER)</a>
                        <ul>
                            <li><a href="#">RAJNI KUMARI</a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">VEENA SASEENDRAN (SENIOR BUSINESS MANAGER)</a>
                        <ul>
                            <li><a href="#">RESHMY RAVEENDARAN</a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">CHETAN VINOBHA SHETTY (DELIVERY HEAD - SEARCH & SELECTION)</a>
                        <ul>
                            <li>
                                <a href="javascript:void(0);">SAVITA JAYSHEEL DESAI (TEAM LEAD - DELIVERY)</a>
                                <ul>
                                    <li><a href="#">ARAVIND K</a></li>
                                    <li><a href="#">ROOHUL AMIN</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">RASHMI K (LEAD TALENT ACQUISITION)</a>
                                <ul>
                                    <li><a href="#">ROHIT GANI</a></li>
                                    <li><a href="#">SHARADHI D A</a></li>
                                    <li><a href="#">KANTEGIRI NIKHIL KUMAR</a></li>
                                    <li><a href="#">ARUN JANARTHAN BHAT</a></li>
                                    <li><a href="#">NAVEEN K DONGARE</a></li>
                                    <li><a href="#">SHILPA</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0);">CHAMPA B V (ASSOCIATE VP - STRATEGIC RECRUITMENTS)</a>
                                <ul>
                                    <li><a href="#">JAYASHREE GANGADHAR NAIK</a></li>
                                    <li><a href="#">INDUKURI KAVITHA </a></li>
                                    <li><a href="#">SARANA AISHWARYA</a></li>
                                    <li><a href="#">PAVITRA</a></li>
                                    <li><a href="#">SUKANYA K</a></li>
                                    <li><a href="#">RAJESH BELAVATAG</a></li>
                                    <li><a href="#">AJITH U</a></li>
                                    <li><a href="#">RANJITHA S</a></li>
                                    <li><a href="#">MEGHANA K R</a></li>
                                    <li><a href="#">SUMEDHA K</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">UDAY KUMAR REDDY S (SENIOR ACCOUNT MANAGER)</a>
                        <ul>
                            <li><a href="#">GIRISHA H</a></li>
                            <li><a href="#">AVINASH KADAM</a></li>
                            <li><a href="#">ARPITH KUMAR</a></li>
                            <li><a href="#">V POORNIMA</a></li>
                            <li><a href="#">NEERAJA G S</a></li>
                            <li><a href="#">GV KUSUMA</a></li>
                            <li><a href="#">SUJITHA J</a></li>
                            <li><a href="#">MOHAMED HASHEEM YUNOS</a></li>
                            <li><a href="#">J BINDHU SEKHAR</a></li>
                            <li><a href="#">RIZWAN ALI KHAN</a></li>
                            <li><a href="#">T VIJAI</a></li>
                            <li><a href="#">NISHA GOSWAMI</a></li>
                            <li><a href="#">KUPPACHI VISWANANDA</a></li>
                            <li><a href="#">AKULA MONESH</a></li>
                            <li><a href="#">DURUVANTHRAJ B G</a></li>
                            <li><a href="#">PRATHIKSHA</a></li>
                            <li><a href="#">SWATHI M</a></li>
                            <li><a href="#">R B ABHISHEK</a></li>
                            <li><a href="#">VINAY KUMAR</a></li>
                            <li><a href="#">SAHIL GADGE</a></li>
                            <li><a href="#">HEMALATHA A</a></li>
                            <li><a href="#">VEDAVYASA K</a></li>
                            <li><a href="#">MADHU V</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0);">BINDU SUNIL (BUSINESS MANAGER)</a>
                        <ul>
                            <li><a href="#">PRADEEP S R</a></li>
                            <li><a href="#">DAVID THANGAM S</a></li>
                            <li><a href="#">BHOOMIKA N</a></li>
                            <li><a href="#">SANGEETHA V S</a></li>
                            <li><a href="#">SUVARNA BILGUNDI</a></li>
                            <li><a href="#">ARUN M T</a></li>
                            <li>
                                <a href="javascript:void(0);">V RAJESHWARI (LEAD TALENT ACQUISITION)</a>
                                <ul>
                                    <li><a href="#">JOYEETA GHOSAL</a></li>
                                    <li><a href="#">PRERNA SEN</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="employee-details" id="employeeDetails">
        <button class="close-btn">&times;</button>
        <h3>Employee Details</h3>
        <div class="detail-item">
            <div class="detail-label">Name</div>
            <div class="detail-value" id="empName"></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Position</div>
            <div class="detail-value" id="empPosition"></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Department</div>
            <div class="detail-value" id="empDepartment"></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value" id="empEmail"></div>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <h4>
                Notes
                <span id="noteCount" style="font-size: 0.8em; color: #95a5a6;"></span>
            </h4>
            <div class="notes-list" id="notesList">
                <!-- Notes will be populated here -->
            </div>
            <form class="add-note-form" id="addNoteForm">
                <textarea 
                    class="note-input" 
                    placeholder="Add a note..."
                    required
                ></textarea>
                <button type="submit" class="add-note-btn">Add</button>
            </form>
        </div>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" id="zoomOut">-</button>
        <div class="zoom-level">100%</div>
        <button class="zoom-btn" id="zoomIn">+</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Load employee data
            let employeeData = {};
            try {
                const response = await fetch('emp-data.txt');
                const text = await response.text();
                const lines = text.split('\n').slice(1); // Skip header row
                
                lines.forEach(line => {
                    if (line.trim()) {
                        const [id, name, email, position, department] = line.split('\t').map(s => s.trim());
                        if (name) {
                            employeeData[name] = {
                                id: id,
                                email: email === 'N/A' ? `${name.toLowerCase().replace(/\s+/g, '.')}@fidelisgroup.in` : email,
                                position: position,
                                department: department
                            };
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading employee data:', error);
            }

            // Notes functionality
            let currentEmployeeId = null;
            const API_BASE_URL = '/api';
            
            const notesManager = {
                async saveNotes(employeeId, notes) {
                    // This function is kept for compatibility but not used with SQLite
                    return true;
                },
                
                async getNotes(employeeId) {
                    try {
                        const response = await fetch(`${API_BASE_URL}/notes/${employeeId}`);
                        if (!response.ok) throw new Error('Failed to fetch notes');
                        return await response.json();
                    } catch (error) {
                        console.error('Error getting notes:', error);
                        return [];
                    }
                },
                
                async addNote(employeeId, noteText) {
                    try {
                        const response = await fetch(`${API_BASE_URL}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                employee_id: employeeId,
                                text: noteText
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to add note');
                        const result = await response.json();
                        return {
                            id: result.id,
                            text: noteText,
                            date: result.created_at,
                            lastEdited: null
                        };
                    } catch (error) {
                        console.error('Error adding note:', error);
                        return null;
                    }
                },
                
                async editNote(employeeId, noteId, newText) {
                    try {
                        const response = await fetch(`${API_BASE_URL}/notes/${noteId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                text: newText
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to edit note');
                        return true;
                    } catch (error) {
                        console.error('Error editing note:', error);
                        return false;
                    }
                },
                
                async deleteNote(employeeId, noteId) {
                    try {
                        const response = await fetch(`${API_BASE_URL}/notes/${noteId}`, {
                            method: 'DELETE'
                        });
                        
                        if (!response.ok) throw new Error('Failed to delete note');
                        return true;
                    } catch (error) {
                        console.error('Error deleting note:', error);
                        return false;
                    }
                }
            };

            function formatDate(dateString) {
                if (!dateString) return ''; // Handle empty input

                const date = new Date(dateString); // Parse the date string
                if (isNaN(date.getTime())) return ''; // Handle invalid dates

                // Format the date using Intl.DateTimeFormat
                return new Intl.DateTimeFormat('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true // This will show AM/PM
                }).format(date);
            }

            async function renderNotes(employeeId) {
                const notesList = document.getElementById('notesList');
                const notes = await notesManager.getNotes(employeeId);
                const noteCount = document.getElementById('noteCount');
                
                if (!notesList || !noteCount) return;
                
                noteCount.textContent = `(${notes.length})`;
                
                if (notes.length === 0) {
                    notesList.innerHTML = '<div class="notes-empty">No notes yet</div>';
                    return;
                }

                console.log('Notes:', notes);
                
                notesList.innerHTML = notes.map(note => `
                    <div class="note-item" data-note-id="${note.id}">
                        <div class="note-date">
                            ${formatDate(note.date)}
                            ${note.lastEdited ? `<span style="color: #bdc3c7">(edited: ${formatDate(note.lastEdited)})</span>` : ''}
                        </div>
                        <div class="note-text">${note.text}</div>
                        <div class="note-actions">
                            <button class="note-action-btn edit-note" title="Edit">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.5-.5V9h-.5A.5.5 0 0 1 0 9v1a.5.5 0 0 1 .5.5h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 0 1 .5.5h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 0 1 .5.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5z"/>
                                </svg>
                            </button>
                            <button class="note-action-btn delete-note" title="Delete">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `).join('');

                // Add event listeners for edit and delete buttons
                notesList.querySelectorAll('.edit-note').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const noteItem = this.closest('.note-item');
                        const noteId = noteItem.dataset.noteId;
                        const noteText = noteItem.querySelector('.note-text').textContent;
                        
                        const newText = prompt('Edit note:', noteText);
                        if (newText && newText !== noteText) {
                            if (await notesManager.editNote(currentEmployeeId, noteId, newText)) {
                                renderNotes(currentEmployeeId);
                            } else {
                                alert('Failed to edit note. Please try again.');
                            }
                        }
                    });
                });

                notesList.querySelectorAll('.delete-note').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const noteId = this.closest('.note-item').dataset.noteId;
                        if (confirm('Are you sure you want to delete this note?')) {
                            if (await notesManager.deleteNote(currentEmployeeId, noteId)) {
                                renderNotes(currentEmployeeId);
                            } else {
                                alert('Failed to delete note. Please try again.');
                            }
                        }
                    });
                });
            }

            // Handle adding new notes
            const addNoteForm = document.getElementById('addNoteForm');
            if (addNoteForm) {
                addNoteForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const noteInput = this.querySelector('.note-input');
                    const noteText = noteInput.value.trim();
                    
                    if (noteText && currentEmployeeId) {
                        const newNote = await notesManager.addNote(currentEmployeeId, noteText);
                        if (newNote) {
                            noteInput.value = '';
                            renderNotes(currentEmployeeId);
                        } else {
                            alert('Failed to add note. Please try again.');
                        }
                    }
                });
            }

            // Update employee details panel
            document.querySelectorAll('.tree a').forEach(node => {
                node.addEventListener('click', function() {
                    const name = this.textContent.split('(')[0].trim();
                    const empData = employeeData[name] || {};
                    currentEmployeeId = empData.id || name.toLowerCase().replace(/[^a-z0-9]/g, '_');
                    
                    document.getElementById('empName').textContent = name;
                    document.getElementById('empPosition').textContent = empData.position || this.textContent.match(/\((.*?)\)/)?.[1] || 'N/A';
                    document.getElementById('empDepartment').textContent = empData.department || 'N/A';
                    document.getElementById('empEmail').textContent = empData.email || `${name.toLowerCase().replace(/\s+/g, '.')}@fidelisgroup.in`;
                    
                    renderNotes(currentEmployeeId);
                    document.getElementById('employeeDetails').classList.add('active');
                });
            });

            // Close button for employee details
            const closeBtn = document.querySelector('.close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    document.getElementById('employeeDetails').classList.remove('active');
                });
            }

            // Expand CEO node by default
            const ceoNode = document.querySelector('.tree > ul > li');
            if (ceoNode) {
                ceoNode.classList.add('expanded');
            }

            // Add click handlers
            document.querySelectorAll('.tree a').forEach(node => {
                node.addEventListener('click', function(e) {
                    e.preventDefault();
                    const li = this.parentElement;
                    const ul = li.parentElement;
                    const siblings = ul.children;
                    const hasChildren = li.querySelector('ul') !== null;

                    // If node has children
                    if (hasChildren) {
                        // Toggle expansion
                        if (li.classList.contains('expanded')) {
                            // Collapse
                            li.classList.remove('expanded', 'active');
                            // Show all siblings
                            Array.from(siblings).forEach(sibling => {
                                sibling.style.display = '';
                            });
                        } else {
                            // Expand
                            // First, collapse any expanded siblings
                            Array.from(siblings).forEach(sibling => {
                                sibling.classList.remove('expanded', 'active');
                                if (sibling !== li) {
                                    sibling.style.display = 'none';
                                }
                            });
                            // Then expand this node
                            li.classList.add('expanded', 'active');
                        }
                    }
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const allNodes = document.querySelectorAll('.tree li');

                if (searchTerm === '') {
                    // Reset to default state
                    allNodes.forEach(node => {
                        node.style.display = '';
                        node.classList.remove('expanded', 'active');
                    });
                    // Expand CEO node
                    const ceoNode = document.querySelector('.tree > ul > li');
                    if (ceoNode) {
                        ceoNode.classList.add('expanded');
                    }
                    return;
                }

                allNodes.forEach(node => {
                    const text = node.querySelector('a').textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        // Show matching node and its ancestors
                        node.style.display = '';
                        let parent = node.parentElement.closest('li');
                        while (parent) {
                            parent.style.display = '';
                            parent.classList.add('expanded');
                            parent = parent.parentElement.closest('li');
                        }
                    } else {
                        node.style.display = 'none';
                    }
                });
            });

            // Breadcrumb navigation
            let activePath = ['Home'];
            
            function updateBreadcrumb() {
                const breadcrumb = document.getElementById('breadcrumb');
                breadcrumb.innerHTML = activePath.map((item, index) => {
                    return `
                        <span onclick="navigateTo(${index})">${item}</span>
                        ${index < activePath.length - 1 ? '<span class="separator">/</span>' : ''}
                    `;
                }).join('');
            }

            window.navigateTo = function(index) {
                activePath = activePath.slice(0, index + 1);
                updateBreadcrumb();
                // Implement navigation logic here
            };

            // Export to PDF
            document.getElementById('exportPDF').addEventListener('click', function() {
                const element = document.getElementById('orgChart');
                const opt = {
                    margin: 1,
                    filename: 'organizational-chart.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
                };

                html2pdf().set(opt).from(element).save();
            });

            // Expand/Collapse All
            document.getElementById('expandAll').addEventListener('click', function() {
                document.querySelectorAll('.tree li').forEach(li => {
                    if (li.querySelector('ul')) {
                        li.classList.add('expanded');
                    }
                });
            });

            document.getElementById('collapseAll').addEventListener('click', function() {
                document.querySelectorAll('.tree li.expanded').forEach(li => {
                    li.classList.remove('expanded', 'active');
                });
            });

            // Zoom functionality
            let currentZoom = 100;
            const orgChart = document.getElementById('orgChart');
            const zoomLevel = document.querySelector('.zoom-level');

            document.getElementById('zoomIn').addEventListener('click', function() {
                if (currentZoom < 200) {
                    currentZoom += 10;
                    updateZoom();
                }
            });

            document.getElementById('zoomOut').addEventListener('click', function() {
                if (currentZoom > 50) {
                    currentZoom -= 10;
                    updateZoom();
                }
            });

            function updateZoom() {
                orgChart.style.transform = `scale(${currentZoom / 100})`;
                zoomLevel.textContent = `${currentZoom}%`;
            }

            // Utility functions
            function getDepartment(position) {
                if (position.includes('HR')) return 'Human Resources';
                if (position.includes('FINANCE')) return 'Finance';
                if (position.includes('IT') || position.includes('DEVELOPER')) return 'Information Technology';
                if (position.includes('MANAGER')) return 'Management';
                return 'General';
            }

            function generateEmail(name) {
                return name.toLowerCase().replace(/\s+/g, '.') + '@fidelisgroup.in';
            }
        });
    </script>
</body>
</html>