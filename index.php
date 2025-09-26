<?php
$con = connect_local_mysqli("gebert");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Login - Sistema Gebert</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/login.css">
    
    <!-- Preload for better performance -->
    <link rel="preload" href="assets/css/login.css" as="style">
    <link rel="preload" href="assets/js/login.js" as="script">
</head>
<body>
    <div class="container-fluid px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <div class="login-container">
                    <div class="card">
                <div class="card-header">
                    <div class="logo-container">
                        <div class="logo-text">GEBERT</div>
                        <div class="logo-subtitle">SEGURANÇA PATRIMONIAL</div>
                    </div>
                    <h5 class="mb-0 mt-2">
                        <i class="bi bi-shield-lock me-1"></i>
                        Acesso ao Sistema
                    </h5>
                    <p class="mb-0 mt-1 opacity-75" style="font-size: 13px;">Faça login para continuar</p>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-2">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i>
                                E-mail
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Digite seu e-mail"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock me-1"></i>
                                Senha
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Digite sua senha"
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                        style="padding: 8px 10px;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-2 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar-me
                            </label>
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-primary btn-login">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Entrar
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="#" class="forgot-password" style="font-size: 13px;">
                                <i class="bi bi-question-circle me-1"></i>
                                Esqueceu sua senha?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
                    <div class="text-center mt-2">
                        <small class="text-footer" style="font-size: 11px;">
                            © <?php echo date('Y'); ?> Gebert Segurança Patrimonial
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/login.js"></script>
</body>
</html>