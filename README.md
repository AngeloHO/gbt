# 🔐 Sistema de Login - Gebert Segurança Patrimonial

Tela de login moderna e responsiva desenvolvida com Bootstrap 5, seguindo a identidade visual da Gebert Segurança Patrimonial.

## 📁 Estrutura do Projeto

```
gebert/
├── index.php              # Página principal de login
├── assets/
│   ├── css/
│   │   └── login.css      # Estilos customizados
│   └── js/
│       └── login.js       # Scripts customizados
└── README.md              # Este arquivo
```

## 🎨 Características do Design

### **Paleta de Cores**
- **Dourado Principal**: `#FFD700`
- **Dourado Escuro**: `#FFA500`
- **Preto**: `#1a1a1a`
- **Cinza Escuro**: `#2d2d2d`
- **Dourado Claro**: `#FFED4E`

### **Identidade Visual**
- Logo "GEBERT" integrada
- Subtítulo "SEGURANÇA PATRIMONIAL"
- Gradientes dourados
- Sombras elegantes
- Bordas arredondadas

## 📱 Responsividade

O sistema é totalmente responsivo com breakpoints otimizados para:

- **📱 Smartphones**: 320px - 768px
- **📱 Tablets**: 769px - 1024px
- **💻 Desktop**: 1025px+
- **🔄 Modo Paisagem**: Otimizado para telas baixas

### **Características Responsivas**
- Layout fluido com Bootstrap Grid
- Fontes e espaçamentos adaptativos
- Touch targets otimizados para mobile
- Prevenção de zoom no iOS (`font-size: 16px` nos inputs)
- Animações reduzidas para usuários com preferências de acessibilidade

## ⚙️ Funcionalidades

### **🔒 Segurança**
- Sanitização de dados com `htmlspecialchars()`
- Validação de campos obrigatórios
- Proteção contra XSS básica
- Estrutura preparada para implementação de autenticação robusta

### **💡 Interatividade**
- **Toggle de senha**: Botão para mostrar/ocultar senha
- **Validação em tempo real**: Feedback visual nos campos
- **Auto-dismiss de alertas**: Alertas desaparecem automaticamente
- **Loading state**: Botão de login mostra estado de carregamento
- **Animações suaves**: Transições elegantes nos elementos

### **🎯 Acessibilidade**
- **Focus indicators**: Indicadores visuais de foco
- **ARIA labels**: Labels apropriados para screen readers
- **Keyboard navigation**: Navegação completa por teclado
- **High contrast**: Cores com contraste adequado
- **Reduced motion**: Respeita preferências de movimento

## 🚀 Tecnologias Utilizadas

- **HTML5**: Estrutura semântica
- **CSS3**: Estilos com Flexbox, Grid e Custom Properties
- **JavaScript (ES6+)**: Interatividade moderna
- **Bootstrap 5.3.2**: Framework CSS responsivo
- **Bootstrap Icons**: Ícones vetoriais
- **PHP 8+**: Backend básico de processamento

## 📋 Como Usar

### **Configuração Básica**
1. Coloque os arquivos em seu servidor web (Apache/Nginx)
2. Certifique-se de que o PHP está habilitado
3. Acesse via `http://localhost/gebert/`

### **Personalização**

#### **Cores**
Edite as variáveis CSS em `assets/css/login.css`:
```css
:root {
    --gebert-gold: #FFD700;
    --gebert-dark-gold: #FFA500;
    --gebert-black: #1a1a1a;
    --gebert-dark-gray: #2d2d2d;
    --gebert-light-gold: #FFED4E;
}
```

#### **Logo e Textos**
Modifique os textos no `index.php`:
```html
<div class="logo-text">GEBERT</div>
<div class="logo-subtitle">SEGURANÇA PATRIMONIAL</div>
```

#### **Comportamentos JavaScript**
Customize as funcionalidades em `assets/js/login.js`

## 🔧 Implementação de Autenticação

Para implementar um sistema de autenticação completo, substitua o código PHP básico por:

```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Sua lógica de autenticação aqui
    // Verificação no banco de dados
    // Hash de senha
    // Sessão de usuário
    
    if (authenticateUser($email, $password)) {
        $_SESSION['user'] = $email;
        header('Location: dashboard.php');
    } else {
        $error = 'Credenciais inválidas.';
    }
}
?>
```

## 🎭 Performance

### **Otimizações Incluídas**
- **Preload de recursos**: CSS e JS carregados antecipadamente
- **CSS externo**: Separação de estilos para cache
- **JavaScript externo**: Scripts modulares e reutilizáveis
- **Minificação possível**: Arquivos preparados para minificação
- **CDN para Bootstrap**: Carregamento rápido de bibliotecas

### **Métricas Estimadas**
- **First Contentful Paint**: < 1.5s
- **Time to Interactive**: < 2.5s
- **Cumulative Layout Shift**: < 0.1

## 🌐 Compatibilidade

### **Navegadores Suportados**
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Opera 76+

### **Dispositivos Testados**
- 📱 iPhone (Safari)
- 📱 Android (Chrome)
- 💻 Desktop (todos os navegadores)
- 📟 Tablets (iPad, Android)

## 📝 Próximos Passos

1. **Implementar autenticação real** com banco de dados
2. **Adicionar recuperação de senha** funcional
3. **Integrar com sistema de sessões**
4. **Implementar 2FA** (autenticação de dois fatores)
5. **Adicionar logs de tentativas de login**
6. **Criar dashboard pós-login**

## 👥 Créditos

Desenvolvido para **Gebert Segurança Patrimonial**
- Design responsivo e moderno
- Foco em usabilidade e acessibilidade
- Otimizado para performance

---

💡 **Dica**: Para melhor performance em produção, considere minificar os arquivos CSS e JS, e implementar um sistema de cache.