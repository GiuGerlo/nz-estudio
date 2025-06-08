# 🏢 Estudio Jurídico NZ

<div align="center">
  <img src="https://img.shields.io/badge/Status-En%20Desarrollo-brightgreen" alt="Estado del Proyecto">
  <img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Versión">
  <img src="https://img.shields.io/badge/Licencia-MIT-green" alt="Licencia">
</div>

## 📋 Descripción
Sitio web profesional para el Estudio Jurídico de la Dra. Nadina Zaranich, diseñado para ofrecer una experiencia de usuario óptima y facilitar el contacto con clientes potenciales.

## ✨ Características Principales

### 🌐 Páginas y Secciones
- **Inicio**: Presentación del estudio y servicios destacados
- **Servicios**: Listado detallado de áreas de práctica
- **Equipo**: Perfiles profesionales del equipo
- **Contacto**: Formulario de contacto y ubicación con Google Maps
- **Login**: Sistema de autenticación para administradores
- **Gestión de Propiedades**: Panel completo para administrar propiedades inmobiliarias
- **Ordenamiento Personalizado**: Interfaz de arrastrar y soltar para ordenar propiedades por categoría

### 📱 Diseño Responsivo
- Adaptable a todos los dispositivos (móviles, tablets, escritorio)
- Navegación intuitiva
- Tiempos de carga optimizados

### 🛠️ Tecnologías Utilizadas

#### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos personalizados
- **Bootstrap 5** - Framework CSS
- **JavaScript** - Interactividad
- **Bootstrap Icons** - Iconografía

#### Backend
- **PHP** - Procesamiento del lado del servidor
- **MySQL** - Base de datos para usuarios y autenticación
- **XAMPP** - Entorno de desarrollo local

## 🚀 Instalación

1. **Requisitos Previos**
   - XAMPP (o servidor web con soporte PHP)
   - Navegador web actualizado

2. **Configuración**
   ```bash
   # Clonar el repositorio
   git clone [URL_DEL_REPOSITORIO]
   
   # Mover los archivos al directorio de XAMPP
   # (Normalmente en C:/xampp/htdocs/)
   ```

3. **Iniciar el Servidor**
   - Iniciar Apache desde el panel de control de XAMPP
   - Abrir el navegador y navegar a: `http://localhost/nz-estudio`

## 🎨 Estructura del Proyecto

```
nz-estudio/
├── assets/
│   ├── css/           # Hojas de estilo
│   ├── js/            # Archivos JavaScript
│   └── img/           # Imágenes y recursos gráficos
├── config/            # Archivos de configuración
│   └── config.php     # Configuración de la base de datos
├── includes/          # Archivos PHP reutilizables
│   ├── auth_check.php # Verificación de autenticación
│   └── head.php       # Encabezado común
├── templates/         # Plantillas de páginas
├── auth.php           # Procesamiento de autenticación
├── login.php          # Página de inicio de sesión
├── logout.php         # Cierre de sesión
├── index.php          # Página de inicio
└── README.md          # Este archivo
```

## 🔐 Sistema de Autenticación

El sitio incluye un sistema de autenticación seguro para el panel de administración.

### Características:
- Validación de formularios en el cliente y el servidor
- Protección contra ataques de inyección SQL
- Manejo de sesiones seguras
- Interfaz de usuario intuitiva con mensajes de retroalimentación

### Credenciales por defecto:
- **Usuario**: admin@example.com
- **Contraseña**: password


## Personalización

### Estilos

- **Archivos principales**:
  - `assets/css/style.css`: Estilos generales
  - `assets/css/propiedades.css`: Estilos específicos para listados
  - `assets/css/responsive.css`: Media queries para diseño adaptativo

### JavaScript

- **Archivos principales**:
  - `assets/js/main.js`: Funcionalidades generales
  - `assets/js/filtros.js`: Filtrado y ordenamiento de propiedades
  - `assets/js/lightbox.js`: Galería de imágenes

## Seguridad

- **Protección contra inyección SQL**: Uso de consultas preparadas PDO
- **Validación de entrada**: Filtrado de datos de usuario
- **Protección XSS**: Escapado de salida
- **Seguridad de sesiones**: Configuración segura de cookies
- **Headers de seguridad**: Configurados en `.htaccess`

## Diseño Responsive

- **Mobile First**: Diseño pensado primero para móviles
- **Breakpoints**:
  - Móvil: < 768px
  - Tablet: 768px - 991px
  - Escritorio: ≥ 992px
- **Imágenes adaptativas**: Uso de `srcset` para diferentes resoluciones

## Optimización

- **Caché**: Headers de caché apropiados
- **Minificación**: CSS y JS minificados en producción
- **Optimización de imágenes**: Compresión automática
- **Lazy loading**: Para imágenes fuera del viewport

## SEO

- **URLs amigables**: Estructura clara y legible
- **Meta etiquetas**: Títulos y descripciones únicas
- **Datos estructurados**: Schema.org para propiedades inmobiliarias
- **Sitemap.xml**: Generación automática
- **robots.txt**: Configuración para motores de búsqueda

## Contacto

- **Estudio Jurídico NZ**  
  📍 Catamarca 227, Guatimozín, Córdoba  
  📧 nadinazaranich@gmail.com  
  📱 3468 52-5227

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, lee las [pautas de contribución](CONTRIBUTING.md) antes de enviar un pull request.

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más información.

---
<div align="center">
  Hecho con ❤️ por el equipo de Artisans Thinking
</div>
