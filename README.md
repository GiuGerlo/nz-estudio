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

### Estructura de la base de datos:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tipos_propiedad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    localidad VARCHAR(100) NOT NULL,
    ubicacion TEXT,
    tamanio VARCHAR(50),
    servicios TEXT,
    caracteristicas TEXT,
    mapa TEXT,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria) REFERENCES tipos_propiedad(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS imagenes_propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_propiedad INT NOT NULL,
    ruta_imagen VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_propiedad) REFERENCES propiedades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar usuario de prueba (contraseña: password)
INSERT INTO users (email, password) 
VALUES ('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

## 🏠 Gestión de Propiedades

El sistema ahora incluye un completo módulo de gestión de propiedades inmobiliarias con las siguientes características:

### Panel de Administración
- Listado de propiedades con vista previa de imágenes
- Búsqueda y filtrado de propiedades
- Paginación y ordenamiento
- Vista de propiedades por categoría

### Gestión de Propiedades
- Creación y edición de propiedades
- Asignación de categorías
- Gestión de imágenes (múltiples por propiedad)
- Conversión automática a WebP para optimización
- Vista previa en tiempo real

### Ordenamiento Personalizado
- Interfaz de arrastrar y soltar
- Ordenamiento por categorías
- Actualización en tiempo real sin recargar la página
- Feedback visual durante el proceso

### Características Técnicas
- Carga optimizada de imágenes
- Interfaz responsiva
- Validación de formularios
- Manejo de errores
- Notificaciones de estado

## 🔒 Seguridad

Se han implementado las siguientes medidas de seguridad:

- **Protección contra inyección SQL**: Uso de consultas preparadas
- **Hash de contraseñas**: Uso de `password_hash()` para almacenar contraseñas de forma segura
- **Validación de entrada**: Filtrado y validación de todos los datos de entrada
- **Manejo de sesiones seguras**: Configuración adecuada de las cookies de sesión
- **Protección CSRF**: Implementada en los formularios críticos

## 🎯 Objetivos del Proyecto

- **Profesionalismo**: Presentar una imagen corporativa seria y confiable
- **Accesibilidad**: Garantizar que el sitio sea accesible para todos los usuarios
- **Rendimiento**: Optimizar los tiempos de carga y la experiencia de usuario
- **Conversión**: Facilitar el contacto con clientes potenciales

## 📞 Contacto

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
