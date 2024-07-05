<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Madridejos HR system</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/images/mad2.png'); ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/images/mad2.png'); ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/images/mad2.png'); ?>" />

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/vendors/styles/core.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/vendors/styles/icon-font.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/vendors/styles/style.css'); ?>" />

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GBZ3SGGX85"></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2973766580778258" crossorigin="anonymous"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-GBZ3SGGX85');
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-NXZMQSS');
    </script>
    <!-- End Google Tag Manager -->
    <style>
        body {
            background-image: url('<?php echo base_url('assets/images/mad.png'); ?>');
            background-size: cover; /* Ensures the image covers the whole background */
            background-repeat: no-repeat; /* Prevents repeating the image */
            background-attachment: fixed; /* Makes the background image fixed when scrolling */
            background-position: none; /* Positions the background image to the center */
        }
    </style>
</head>

<body>
    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <a href="<?php echo base_url('login'); ?>">
                </a>
            </div>
            <div class="login-menu">
                <ul>
                    <li><a href="<?php echo base_url('login'); ?>">Login</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center text-primary">Forgot Password</h2>
                        </div>
                        <h6 class="mb-20">Enter your email address to reset your password</h6>
                        <form id="resetForm" action="<?php echo base_url('login/forgot_password2'); ?>" method="post" onsubmit="return handleFormSubmit(event);">
                            <!-- CSRF Token -->
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            <div class="input-group custom">
                                <input type="text" class="form-control form-control-lg" name="email" id="email" placeholder="Email" />
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <div class="input-group mb-0">
                                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="font-16 weight-600 text-center" data-color="#707373">OR</div>
                                </div>
                                <div class="col-5">
                                    <div class="input-group mb-0">
                                        <a class="btn btn-outline-primary btn-lg btn-block" href="<?php echo base_url('login'); ?>">Login</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- js -->
    <script src="<?php echo base_url('assets/vendors/scripts/core.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendors/scripts/script.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendors/scripts/process.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendors/scripts/layout-settings.js'); ?>"></script>

    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NXZMQSS" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>

    <!-- Custom JavaScript to handle form submission and display alerts -->
    <script>
        function handleFormSubmit(event) {
            event.preventDefault();
            var form = document.getElementById('resetForm');
            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                var messageDiv = document.getElementById('message');
                messageDiv.innerHTML = ''; // Clear previous messages
                
                var alertDiv = document.createElement('div');
                alertDiv.className = 'alert';
                alertDiv.innerHTML = data.message;

                if (data.status === 'success') {
                    alertDiv.classList.add('alert-success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000); // Delay for 2 seconds before refreshing
                } else {
                    alertDiv.classList.add('alert-danger');
                }

                messageDiv.appendChild(alertDiv);
            })
            .catch(error => {
                var messageDiv = document.getElementById('message');
                messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
            });
        }
    </script>
</body>
</html>
