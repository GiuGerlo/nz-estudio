-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 03, 2026 at 02:11 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u407412506_nzestudio`
--

-- --------------------------------------------------------

--
-- Table structure for table `imagenes_propiedades`
--

CREATE TABLE `imagenes_propiedades` (
  `id` int(11) NOT NULL,
  `id_propiedad` int(11) DEFAULT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imagenes_propiedades`
--

INSERT INTO `imagenes_propiedades` (`id`, `id_propiedad`, `ruta_imagen`) VALUES
(18, 7, 'uploads/propiedades/casas/7/68421a17ce24a_img_casa21_1.webp'),
(19, 8, 'uploads/propiedades/casas/8/68431d6aa91fa_img_casa20_1.webp'),
(20, 9, 'uploads/propiedades/casas/9/68431eb8ae4d7_img_casa19_1.webp'),
(21, 10, 'uploads/propiedades/casas/10/68431fa1cb71a_img_casa18_1.webp'),
(22, 11, 'uploads/propiedades/casas/11/6843204889987_img_casa17_1.webp'),
(23, 12, 'uploads/propiedades/casas/12/6843211b18279_img_casa16_1.webp'),
(25, 15, 'uploads/propiedades/casas/15/6843236034851_img_casa13_1.webp'),
(26, 16, 'uploads/propiedades/casas/16/684324083d553_img_casa11_1.webp'),
(27, 17, 'uploads/propiedades/casas/17/68432500561ee_img_casa10_1.webp'),
(31, 21, 'uploads/propiedades/casas/21/684430d83b0da_img_casa4_1.webp'),
(32, 22, 'uploads/propiedades/casas/22/6844316c47320_img_casa2_1.webp'),
(33, 23, 'uploads/propiedades/casas/23/68458c76f2cf8_img_casa1_1.webp'),
(34, 24, 'uploads/propiedades/casas/24/68458d5e5303a_img_casa22_1.webp'),
(54, 27, 'uploads/propiedades/cocheras/27/6845945a072db_img_cochera1_1.webp'),
(55, 27, 'uploads/propiedades/cocheras/27/6845945a253d6_img_cochera1_2.webp'),
(56, 27, 'uploads/propiedades/cocheras/27/6845945a46ad4_img_cochera1_3.webp'),
(57, 28, 'uploads/propiedades/casas/28/684599334806d_img_casa25_1.webp'),
(66, 30, 'uploads/propiedades/terrenos/30/68459cf4406e6_img_terreno6_1.webp'),
(67, 31, 'uploads/propiedades/terrenos/31/68459d541876c_img_terreno5_1.webp'),
(69, 33, 'uploads/propiedades/terrenos/33/68459e5aa4304_img_terreno3_1.webp'),
(70, 34, 'uploads/propiedades/terrenos/34/68459ef4f1497_img_terreno1_1.webp'),
(71, 35, 'uploads/propiedades/terrenos/35/68459f445f947_img_terreno8_1.webp'),
(72, 36, 'uploads/propiedades/terrenos/36/68459f92641db_img_terreno9_1.webp'),
(73, 37, 'uploads/propiedades/terrenos/37/68459fea63bd7_img_terreno10_1.webp'),
(74, 37, 'uploads/propiedades/terrenos/37/68459fea9affe_img_terreno10_2.webp'),
(75, 38, 'uploads/propiedades/terrenos/38/6845a046650cf_img_terreno11_1.webp'),
(76, 39, 'uploads/propiedades/locales/39/6845a0be090fc_img_local1_1.webp'),
(78, 41, 'uploads/propiedades/quintas/41/6845a17f37d0a_img_quinta2_1.webp'),
(79, 42, 'uploads/propiedades/departamentos/42/6845a2032f8fe_img_depto1_1.webp'),
(80, 43, 'uploads/propiedades/casas/43/6845a29ebda74_vendida1.webp'),
(81, 44, 'uploads/propiedades/casas/44/6845a4b7dea4f_vendida2.webp'),
(82, 45, 'uploads/propiedades/quintas/45/6845a5235af46_vendida3.webp'),
(83, 46, 'uploads/propiedades/casas/46/6845a5634dc34_vendida4.webp'),
(84, 47, 'uploads/propiedades/casas/47/6845a5cc4bc4b_vendida5.webp'),
(85, 48, 'uploads/propiedades/terrenos/48/6845a5f86a972_vendida6.webp'),
(86, 49, 'uploads/propiedades/casas/49/6845a63176023_vendida7.webp'),
(87, 50, 'uploads/propiedades/terrenos/50/6845a65d8fa3d_vendida8.webp'),
(88, 51, 'uploads/propiedades/terrenos/51/6845a7036b72b_vendida9.webp'),
(89, 52, 'uploads/propiedades/casas/52/6845a7377818e_vendida10.webp'),
(91, 32, 'uploads/propiedades/terrenos/32/684cdc8c48c26_Casa_NZ__1_.webp'),
(94, 53, 'uploads/propiedades/locales/53/687ff0fbf13ce_Imagen_de_WhatsApp_2025_07_22_a_las_11.07.25_8e6b0c8b.webp'),
(107, 55, 'uploads/propiedades/casas/55/6883fe223d91a_casa1.webp'),
(108, 55, 'uploads/propiedades/casas/55/6883fe225df1f_casa2.webp'),
(109, 55, 'uploads/propiedades/casas/55/6883fe227b6c8_casa3.webp'),
(110, 55, 'uploads/propiedades/casas/55/6883fe229b299_casa4.webp'),
(111, 55, 'uploads/propiedades/casas/55/6883fe22b6975_casa5.webp'),
(112, 55, 'uploads/propiedades/casas/55/6883fe22cf35f_casa6.webp'),
(113, 55, 'uploads/propiedades/casas/55/6883fe22e7305_casa7.webp'),
(114, 55, 'uploads/propiedades/casas/55/6883fe231300a_casa8.webp'),
(115, 55, 'uploads/propiedades/casas/55/6883fe233156b_casa9.webp'),
(116, 55, 'uploads/propiedades/casas/55/6883fe23472f7_casa10.webp'),
(117, 55, 'uploads/propiedades/casas/55/6883fe23653fe_casa11.webp'),
(118, 56, 'uploads/propiedades/casas/56/6887f7475e56c_Imagen_de_WhatsApp_2025_07_28_a_las_18.57.40_55124906.webp'),
(119, 57, 'uploads/propiedades/casas/57/68894d586115b_Imagen_de_WhatsApp_2025_07_29_a_las_10.38.46_2828431f.webp'),
(120, 58, 'uploads/propiedades/locales_comerciales_con_casa/58/688a9fcf8fb02_Imagen_de_WhatsApp_2025_07_30_a_las_10.31.15_8cbae8b3.webp'),
(121, 59, 'uploads/propiedades/terrenos/59/6892786fe5033_Imagen_de_WhatsApp_2025_08_05_a_las_10.31.23_eebf531a.webp'),
(122, 60, 'uploads/propiedades/casas/60/689bc187c2475_Imagen_de_WhatsApp_2025_08_12_a_las_19.05.13_f50d99ad.webp'),
(123, 61, 'uploads/propiedades/casas/61/68e6e22343e4b_Imagen_de_WhatsApp_2025_10_07_a_las_10.25.22_31a3c395.webp'),
(124, 61, 'uploads/propiedades/casas/61/68e6e223834d8_Imagen_de_WhatsApp_2025_10_07_a_las_10.25.25_b4b70a43.webp'),
(125, 61, 'uploads/propiedades/casas/61/68e6e223adc08_Imagen_de_WhatsApp_2025_10_07_a_las_10.25.27_7af94551.webp'),
(126, 61, 'uploads/propiedades/casas/61/68e6e223dad3f_Imagen_de_WhatsApp_2025_10_07_a_las_10.25.29_1e64808b.webp'),
(127, 62, 'uploads/propiedades/locales_comerciales_con_casa/62/68f01b812e867_Imagen_de_WhatsApp_2025_10_15_a_las_18.31.55_fd4426b6.webp'),
(128, 63, 'uploads/propiedades/terrenos/63/6913c341ad548_Imagen_de_WhatsApp_2025_11_11_a_las_17.40.47_71ccf192.webp'),
(129, 64, 'uploads/propiedades/terrenos/64/69fbbf97755e9_WhatsApp_Image_2026_05_06_at_19.21.44.webp'),
(130, 65, 'uploads/propiedades/terrenos/65/69fbc114d9bb0_WhatsApp_Image_2026_05_06_at_19.21.49.webp'),
(131, 65, 'uploads/propiedades/terrenos/65/69fbc1151b657_WhatsApp_Image_2026_05_06_at_19.21.46.webp');

-- --------------------------------------------------------

--
-- Table structure for table `propiedades`
--

CREATE TABLE `propiedades` (
  `id` int(11) NOT NULL,
  `categoria` int(11) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `localidad` varchar(255) DEFAULT NULL,
  `ubicacion` text DEFAULT NULL,
  `tamanio` varchar(255) DEFAULT NULL,
  `servicios` text DEFAULT NULL,
  `caracteristicas` text DEFAULT NULL,
  `mapa` text DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `vendida` tinyint(1) DEFAULT 0,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propiedades`
--

INSERT INTO `propiedades` (`id`, `categoria`, `titulo`, `localidad`, `ubicacion`, `tamanio`, `servicios`, `caracteristicas`, `mapa`, `orden`, `vendida`, `latitud`, `longitud`) VALUES
(7, 1, 'Propiedad esquina Santa Fe y Salta', 'Guatimozín', 'Esquina Santa Fe y Salta', ' Superficie terreno: 352 m2, superficie construida: 76 m2', 'Agua corriente, Electricidad, Cordón cuneta, Luminarias de calle y Asfalto', 'Cocina-Comedor, Baño, Dos habitaciones, Galería y Patio', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d208.0222838403494!2d-62.43791194932969!3d-33.46606697616271!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1729187716738!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46606980, -62.43792273),
(8, 1, 'Propiedad Calle Brasil', 'Guatimozín', 'Calle Brasil, entre Bolivia y Perú', 'Superficie terreno: 1652 m2, superficie construida: 72 m2', 'Agua corriente, Agua caliente en baño y cocina, Electricidad, Cordón cuneta, Luminarias de calle', 'Cocina-comedor, Baño, Dos habitaciones, Ubicada en amplio terreno', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d699.7590077763567!2d-62.42961838774843!3d-33.45885217567194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1729187224301!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.45881060, -62.42994791),
(9, 1, 'Propiedad Calle Santa Fe 100', 'Guatimozín', 'Calle Santa Fe al 100, entre Av. Río de la Plata y Buenos Aires', 'Superficie terreno: 355 m2, superficie construída: 170 m2', 'Agua corriente, Electricidad, Cordón cuneta, Asfalto, Luminarias de calle', 'Hall de ingreso, Comedor, Cocina, Tres dormitorios, Baño, Galería en el patio, Salón, Garage, Amplio patio', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d699.73275115145!2d-62.436878747473585!3d-33.46210521519733!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1723041576609!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46189578, -62.43691655),
(10, 1, 'Propiedad Calle Mendoza 228', 'Guatimozín', 'Calle Mendoza 228, entre Buenos Aires y Catamarca', 'Construcción de 118 m2, sobre un terreno de 244 m2', 'Electricidad, Agua potable, Agua caliente en cocina y baño, Cordón cuneta, Luminarias de calle, Asfalto', 'Dos habitaciones, Baño, Living, Comedor, Cocina, Galería, Patio, Depósito, Cochera', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d832.1228662780733!2d-62.441258698311145!3d-33.46255317529585!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1722445116371!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 1, -33.46228447, -62.44111270),
(11, 7, 'Local comercial y casa Buenos Aires y San Luis', 'Guatimozín', 'Intersección de las calles Buenos Aires y San Luis, Zona Centro', 'Construcción de 269 m2, sobre un terreno de 328 m2', 'Electricidad, Agua potable, Cordón cuneta, Luminarias de calle, Asfalto', 'Casa, Dos locales comerciales en esquina , Oficina sobre calle Buenos Aires', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d247.39270018781488!2d-62.44001743291595!3d-33.46217056347444!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1720651978897!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46211450, -62.44002773),
(12, 1, 'Propiedad ingreso a Guatímozin', 'Guatimozín', 'Ingreso a Guatimozín', 'Terreno de 945 m2', 'Electricidad, Agua potable, Iluminaria de ruta', 'En construcción', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3328.4991010374106!2d-62.42934718189053!3d-33.462354304065826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1718721181209!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46335667, -62.42735154),
(15, 1, 'Propiedad Calle San Luis 400', 'Guatimozín', 'Calle San Luis 400, entre Tucumán y Salta', 'Superficie terreno: 354 m2, superficie construída: 104 m2', 'Electricidad, Agua potable, Pavimento', 'Pasillo, Cocina-Comedor, Baño, Dos habitaciones, Lavadero, Garage, Patio, Amoblamiento de cocina', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d699.7095188305642!2d-62.4405024619757!3d-33.46498332845649!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1717013732787!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 1, -33.46481406, -62.44038130),
(16, 1, 'Propiedad Pasaje Manuel Belgrano S/N', 'Guatimozín', 'Pasaje Manuel Belgrano S/N, entre Jujuy y Chaco', 'Superficie terreno: 235 m2, superficie construída: 66 m2', 'Electricidad, Agua potable, Pavimento.', 'Cocina, Comedor, Pasillo, Lavadero, Baño, Dos habitaciones, Patio. La misma cuenta con Baño instalado, Amoblamiento de cocina con aberturas y puertas de madera, Instalación de gas adentro', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d699.6846826885882!2d-62.43609832319712!3d-33.46805988764727!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1717012062089!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46788883, -62.43619724),
(17, 1, 'Propiedad Calle Salta 200', 'Guatimozín', 'Calle Salta 200, entre Santa Fe y Entre Ríos', 'Superficie terreno: 682,50 m2, superficie construída: 196 m2', 'Electricidad, Agua potable, Pavimento', 'Living, Cocina, Tres dormitorios, Baño, Cochera, Garage, Lavadero, Depósito, Amplio patio', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d832.0880299001637!2d-62.43704124467517!3d-33.46618212822294!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1717011278761!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46613054, -62.43691225),
(21, 7, 'Local comercial y casa Calle Santa Fe 500', 'Guatimozín', 'Calle Santa Fe 500, entre Salta y Jujuy', 'Superficie terreno: 795 m2, superficie construída: 174 m2', 'Electricidad, Agua potable, Pavimento', 'Local comercial al frente (cocina y baño), Casa al costado y atras, Un dormitorio, Baño, Cocina, Amplio patio', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d989.5213171398295!2d-62.43787978957962!3d-33.46650519886027!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1716904709967!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46646252, -62.43803796),
(22, 7, 'Local comercial y casa Calle San Luis 200', 'Guatimozín', 'Calle San Luis al 200, entre Buenos Aires y Catamarca', 'Superficie terreno total: 992 m2, superficie lote N º 1: 550 m2, superficie lote N º 2: 442,25 m2, superficie construída: 221 m2', 'En el terreno sobre calle San Luis se encuentra el local comercial y el depósito en buen estado de conservación, En el terreno con dos ingresos se ubica la casa que está en regular estado de conservación', 'Electricidad, Agua potable, Pavimento', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1664.2432078232875!2d-62.43990544384325!3d-33.46268468379829!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1716841628789!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46261534, -62.44001055),
(23, 1, 'Propiedad Calle Perú', 'Guatimozín', 'Calle Perú S/N, entre Paraguay y Brasil, Barrio Baima', 'Terreno: 1.200 m2 (30 m de frente x 40 m de fondo) y 122 m2 construidos', 'Electricidad, Agua potable, Televisión por cable', 'Importante terreno, Casa', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d961.4306861759359!2d-62.42902658327036!3d-33.45937173019247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1719579598450!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.45956220, -62.42930059),
(24, 1, 'Propiedad Calle Corrientes 172', 'Guatimozín', 'Calle Corrientes 172, entre Buenos Aires y Av. Río de la Plata', 'Superficie terreno: 197 m2, superficie construída: 145 m2', 'Electricidad, Gas natural, Agua corriente, Agua caliente en baño y cocina, Cordón cuneta, Luminarias de calle, Asfalto', 'Cocina, Living-Comedor, Dos dormitorios, Baño, Cochera, Patio pequeño', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1399.4550603521284!2d-62.43473886658694!3d-33.46275202966086!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95c8e7459da528d3%3A0x6aa2d7f57c55bfcd!2sCorrientes%20172%2C%20X2627%20Guatimozin%2C%20C%C3%B3rdoba!5e0!3m2!1ses-419!2sar!4v1736981674451!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 1, -33.46292858, -62.43434282),
(27, 5, 'Cochera Rosario', 'Rosario', 'España 948, ubicada en zona comercial, a 300 m de la Bolsa de Comercio y 100 m del Shopping del Siglo', 'Superficie cubierta: 15,2 m2', '', 'Ubicada en subsuelo bajada tres rampas, Rampas fijas y Ascensor, Abierta las 24hs, Dos años de antiguedad', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1161.1797107939626!2d-60.64771309668526!3d-32.94674519936631!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95b7aba7ed994c67%3A0x671c7d8801292ca5!2sSmart%20Park!5e0!3m2!1ses-419!2sar!4v1747772277611!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -32.94664622, -60.64691670),
(28, 1, 'Propiedad Calle Jujuy S/N', 'Guatimozín', 'Calle Jujuy S/N', 'Superficie terreno: 500 m2, superficie construída: 91 m2', 'Electricidad, Agua potable, Cordón cuneta, Luminarias de calle, Pavimento', 'Dos habitaciones, Baño, Living-Comedor, Cocina, Garaje, Amplio patio, Cañería nueva de agua, Pozos nuevos, Baño totalmente renovado', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d832.0826260627808!2d-62.44019933579185!3d-33.466745022170535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1748552197470!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46671144, -62.44019900),
(30, 2, 'Terreno Calle Perú', 'Guatimozín', 'Calle Perú S/N (entre Paraguay y Uruguay), Barrio Baima, Zona Norte', '560 m2 (14 m de frente x 40 m de fondo)', 'Sin electricidad, Pavimento', 'Sin cerramiento', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1018.5878981147404!2d-62.42940767558271!3d-33.460428641195634!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1716757159415!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46061400, -62.42941000),
(31, 2, 'Terreno Calle Jujuy 100', 'Guatimozín', 'Jujuy al 100 (entre Corrientes y Santiago del Estero), Zona Sur', '675 m2 (15 m de frente x 45 m de fondo)', 'Electricidad, Agua potable, Perforación para agua de pozo', 'Terreno limpio, Semi cercado (falta portón de ingreso)', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d757.0285769026752!2d-62.434529498175664!3d-33.46767252130814!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1749850906327!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46768779, -62.43477597),
(32, 2, 'Terreno Calle Jujuy 642', 'Guatimozín', 'En zona sur de Guatimozín, a media cuadra del Club Atlético Guatimozín. Calle Jujuy N º 642 de Guatimozín', '438.30 m2', 'Electricidad, Agua potable', 'Ingreso de 4 metros, Ubicado en la parte posterior de la vivienda, Garaje con techo de chapas', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1399.4042313980801!2d-62.4428582064381!3d-33.4659004121836!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1716755522781!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46618900, -62.44311500),
(33, 2, 'Terreno Calle San Luis', 'Guatimozín', 'En el centro de Guatimozín, a media cuadra de la Av. Río de la Plata. Calle San Luis al 100 (entre Buenos Aires y Av. Río de la Plata)', '537 m2', 'Electricidad, Agua potable, Pavimento', 'Terreno limpio, Compuesto por tres lotes, Cerrado al frente con lajas, Doble ingreso por Calle San Luis y por calle Buenos Aires (pasillo)', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1399.4709357518789!2d-62.43945098969893!3d-33.461768642166554!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1716754673227!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46197100, -62.43979993),
(34, 2, 'Terreno Calle Rio Negro', 'Guatimozín', 'Calle Río Negro, Barrio Baima, Zona Norte de Guatimozín', 'Lote Entero: 3340 m2, Lote N º 1 (1670 m2), Lote N º 2 (1670 m2)', 'En el Lote N º 1 está la perforación para agua de pozo y la bajada para la electricidad', 'Terreno limpio, A la venta en su totalidad o en dos lotes, Cercado con alambrado', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1176.9071568302477!2d-62.436486924398984!3d-33.45461773696181!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1716753723602!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.45426881, -62.43641873),
(35, 2, 'Terreno Calle Santa Fe al 100', 'Guatimozín', 'Calle Santa Fe al 100, Zona Centro', '188 m2 (12 m de frente x 15 m de fondo)', '', 'Terreno limpio, Con cerramiento', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d699.73275115145!2d-62.436878747473585!3d-33.46210521519733!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1723041576609!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 1, -33.46198305, -62.43688168),
(36, 2, 'Terreno Calle La Rioja', 'Guatimozín', 'Calle La Rioja S/N, entre Salta y Jujuy', '474 m2 (12 m2 de frente x 39,5 m2 de fondo)', 'Cordón cuneta, Luminarias de calle, Asfalto', 'Terreno limpio, Sin cerramientos', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d699.7072006277631!2d-62.444727251711946!3d-33.46527050473289!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1746728219282!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 1, -33.46532806, -62.44472164),
(37, 2, 'Terreno Calle San Juan', 'Guatimozín', 'Calle San Juan S/N, entre Salta y Tucumán', '1643 m2 (15,5 m2 de frente x 106 m2 de fondo)', 'Electricidad, Agua potable, Cordón cuneta, Luminarias de calle, Asfalto', 'Terreno limpio, Sin cerramientos, Doble acceso por calles San Juan y La Rioja', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d699.7135426820643!2d-62.443202247355046!3d-33.464484853225706!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1746728613134!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46445226, -62.44320318),
(38, 2, 'Terreno Cavanagh', 'Cavanagh', 'Esquina Alvear y España', '720 m2 (16 m2 de frente x 45 m2 de fondo)', 'Agua potable, Gas, Cordón cuneta, Luminarias de calle, Adoquinado', 'Terreno limpio, Sin cerramientos', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d856.3374551933658!2d-62.34078709156255!3d-33.47959992653142!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1746729021388!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.47950380, -62.34080309),
(39, 3, 'Local Calle Salta 100', 'Guatimozín', 'Calle Salta 100, entre Corrientes y Entre Ríos', '675 m2 de terreno y construidos 165 m2', 'Electricidad, Agua potable, Pavimento', '', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d588.3742164587268!2d-62.4357971435823!3d-33.466310589472634!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1719581872435!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.46630988, -62.43584260),
(41, 4, 'Quinta Zona Norte 2', 'Guatimozín', 'Zona Norte de Guatimozín', '2 hectáreas', 'Electricidad', 'Cuenta con alambrados, Tranquera de ingreso', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1018.6861050883415!2d-62.43753313507137!3d-33.45206908208118!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95c8e794153f6233%3A0x28ea90686c5a914a!2sQuinta%20manavalla!5e0!3m2!1ses-419!2sar!4v1719582021215!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\" class=\\\"rounded-2\\\"></iframe>', 0, 0, -33.45191344, -62.43760403),
(42, 6, 'Departamento Calle Zeballos 1565', 'Rosario', 'Calle Zeballos N° 1565, Piso 9', 'Superficie de propiedad exclusiva: 32,66 m2', 'Agua corriente, Gas, Electricidad, Servicios cloacales,  Asfalto', 'Living-Comedor, Cocina, Dormitorio, Baño', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3347.8878907383723!2d-60.64939552481823!3d-32.95396917225766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95b7ab123c628e0f%3A0x76d8e2cc85a6823!2sZeballos%201565%2C%20S2000BQE%20Rosario%2C%20Santa%20Fe!5e0!3m2!1ses-419!2sar!4v1749846068955!5m2!1ses-419!2sar\\\" width=\\\"600\\\" height=\\\"450\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\"></iframe>', 0, 0, -32.95352226, -60.64690838),
(43, 1, 'Propiedad Calle Entre Ríos 200', 'Guatimozín', 'Calle Entre Ríos 200', '', '', '', '', 0, 1, NULL, NULL),
(44, 1, 'Propiedad Tucumán 50', 'Guatimozín', 'Calle Tucumán 50', '', '', '', '', 0, 1, NULL, NULL),
(45, 4, 'Quinta Calle Río Paraná y Santiago del Estero Norte', 'Guatimozín', 'Calle Río Paraná y Santiago del Estero Norte', '', '', '', '', 0, 1, NULL, NULL),
(46, 1, 'Propiedad Calle Salta 100', 'Guatimozín', 'Calle Salta 100', '', '', '', '', 0, 1, NULL, NULL),
(47, 1, 'Propiedad Av. Córdoba 300', 'Guatimozín', 'Av. Córdoba 300', '', '', '', '', 0, 1, NULL, NULL),
(48, 2, 'Terreno en Funes', 'Funes', 'Funes', '', '', '', '', 0, 1, NULL, NULL),
(49, 1, 'Propiedad Calle San Juan 300', 'Guatimozín', 'Calle San Juan 300', '', '', '', '', 0, 1, NULL, NULL),
(50, 2, 'Terreno Calle Mendoza', 'Guatimozín', 'Calle Mendoza al 500, Zona Sur de Guatimozín', '', '', '', '', 0, 1, NULL, NULL),
(51, 2, 'Terreno Calle Jujuy', 'Guatimozín', 'Jujuy S/N (entre La Rioja y San Juan)', '', '', '', '', 0, 1, NULL, NULL),
(52, 1, 'Propiedad Calle Mendoza 366', 'Guatimozín', 'Calle Mendoza 366', '', '', '', '', 0, 1, NULL, NULL),
(53, 7, 'Local comercial y Casa Calle Santa Fe S/N', 'Guatimozín', 'Calle Santa Fe S/N', 'Superficie terreno 352 m2 ; superficie construida 209 m2', 'Agua corriente, Agua caliente en cocina y baño, Electricidad, Cordón cuneta, Luminarias de calle, Asfalto', 'Comedor, Cocina, Dos dormitorios, Baño, Patio', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d760.5116534855819!2d-62.438135730139734!3d-33.46653673047971!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1753215565353!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46629469, -62.43800521),
(55, 1, 'Propiedad Calle Salta 614', 'Guatimozín', 'Calle Salta 614', 'Superficie total: 252m2, superficie construida: 191m2', 'Agua caliente, Electricidad, Cordon cuneta, Asfalto, Luminarias de calle', 'Living, Cocina-comedor, Tres dormitorios, Baño, Galeria cerrada, Patio con aljibe-asador y depósito, Garage con portón levadizo', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d832.0963855860019!2d-62.442377088205404!3d-33.46531173674929!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1753480263812!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46516741, -62.44228589),
(56, 7, 'Local comercial y casa Calle Mitre 350 Cavanagh', 'Cavanagh', 'Calle Mitre 350', 'Superficie del terreno: 795 m², superficie de la propiedad: 236 m²', 'Agua caliente para baños y cocina, Electricidad, Alcantarillado, Asfalto, Alumbrado público', 'Cocina-comedor, Sala de estar, Dos dormitorios, Baño, Local comercial (al frente), Patio (con cisterna), Piscina', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d494.7094860298425!2d-62.33700942237502!3d-33.475468787473766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1753741113973!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.47540493, -62.33700862),
(57, 1, 'Propiedad Calle Uruguay S/N', 'Guatimozín', 'Calle Uruguay S/N, Barrio Baima', 'Superficie terreno: 826m2, superficie de propiedad: 88m2', 'Electricidad, Cordón cuneta, Asfalto, Luminarias de calle', 'Comedor, Cocina, Tres dormitorios, Baño, Lavadero, Amplio patio con asador, Garage', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d699.740176052856!2d-62.42874131976479!3d-33.46118534245132!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1753828691654!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46134528, -62.42891612),
(58, 7, 'Local comercial y Casa Calle España 683', 'Cavanagh', 'Calle España 683', 'Superficie de terreno: 1440m2, superficie construida: 424m2', 'Electricidad, Agua potable, Arenado, Luminarias de calle', '2 habitaciones, Cocina-comedor, Baño, Patio, Local comercial: 8x8m2, Entrepiso, Cocina, Baño', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d831.9657758523763!2d-62.3447061675901!3d-33.47891474863051!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95c8e5cfd3cfc793%3A0xe0b0f505f0fb5412!2zRXNwYcOxYSA2ODMsIENhdmFuYWdoLCBDw7NyZG9iYQ!5e0!3m2!1ses-419!2sar!4v1753915336403!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.47885882, -62.34432664),
(59, 2, 'Terreno Barrio Baima', 'Guatimozín', 'Barrio Baima', 'Superficie: 560m2', 'Electricidad, Agua potable, Cordón cuneta, Arenado', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3328.6990839837376!2d-62.43142848502933!3d-33.45714554498142!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1754429464845!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.45642051, -62.43265694),
(60, 1, 'Propiedad Calle Salta 448', 'Guatimozín', 'Calle Salta 448', 'Superficie de terreno: 228m2, superficie construida: 83m2', 'Luminarias de calle, pavimento, agua, electricidad', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d416.04700838185926!2d-62.4399966726175!3d-33.465558493237545!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1755038068165!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46559653, -62.44001411),
(61, 1, 'Propiedad en Zona Quintas', 'Guatimozín', 'Zona de quintas', 'Superficie del terreno: 1.260m2, superficie construida: 220m2', '', '3 habitaciones 1 con vestidor, 3 baños, Cocina, Living-Comedor, Amplio garage, Patio con pileta de natación', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1399.3485989116766!2d-62.43175169858667!3d-33.469346029049944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m3!3e1!4m0!4m0!5e0!3m2!1ses-419!2sar!4v1759961578040!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46918422, -62.43136601),
(62, 7, 'Local Comercial y Casa Calle San Luis 400', 'Guatimozín', 'Calle San Luis al 400', '', '', 'Garage para 2 autos, Amplio comedor con sótano, Cocina, Patio con pileta de natación, Planta Alta, Living-comedor, Cocina, Lavadero, 3 habitaciones, Baño, Terraza con asador', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3328.40265035414!2d-62.440759383451166!3d-33.46486620405695!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1760565705380!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46510704, -62.44047520),
(63, 2, 'Terreno Calle Catamarca al 600', 'Guatimozín', 'Calle Catamarca al 600', 'Superficie: 346m2', '', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1664.2376356889722!2d-62.44203606410267!3d-33.46297492399385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1762902807766!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46302863, -62.44210848),
(64, 2, 'Terreno Calle Corrientes', 'Guatimozín', 'Calle Corrientes, entre Tucumán y Salta', 'Superficie: 232,50 m2 (15x15,50)', 'Electricidad, Agua potable, Cordón cuneta, Pavimento', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d548.9741313667371!2d-62.43509829701667!3d-33.46606390217602!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1778106256514!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46617536, -62.43502928),
(65, 2, 'Terreno esquina Entre Ríos y Salta', 'Guatimozín', 'Esquina Entre Ríos y Salta', 'Superficie: 519 m2', 'Electricidad, Agua potable, Cordón cuneta, Pavimento', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d362.1865036853567!2d-62.436433753983714!3d-33.466389333539645!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1778106582621!5m2!1ses-419!2sar\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 0, 0, -33.46637424, -62.43647111);

-- --------------------------------------------------------

--
-- Table structure for table `tipos_propiedad`
--

CREATE TABLE `tipos_propiedad` (
  `id` int(11) NOT NULL,
  `nombre_categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipos_propiedad`
--

INSERT INTO `tipos_propiedad` (`id`, `nombre_categoria`) VALUES
(1, 'Casas'),
(2, 'Terrenos'),
(3, 'Locales'),
(4, 'Quintas'),
(5, 'Cocheras'),
(6, 'Departamentos'),
(7, 'Locales comerciales con Casa');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(1, 'ggiuliano526@gmail.com', '$2y$10$yRNy2f0OR/axuwVSFSnQ8uIosyGgSG4FKu.T44Tktyr46d5hw5rR2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_propiedad` (`id_propiedad`);

--
-- Indexes for table `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria` (`categoria`);

--
-- Indexes for table `tipos_propiedad`
--
ALTER TABLE `tipos_propiedad`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `tipos_propiedad`
--
ALTER TABLE `tipos_propiedad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD CONSTRAINT `imagenes_propiedades_ibfk_1` FOREIGN KEY (`id_propiedad`) REFERENCES `propiedades` (`id`);

--
-- Constraints for table `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `tipos_propiedad` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
