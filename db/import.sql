DROP DATABASE IF EXISTS eindproject;
CREATE DATABASE eindproject;
USE eindproject;

CREATE TABLE items (
    id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    rating DECIMAL (2,1) NULL,
    prijs DECIMAL (10,2) NOT NULL,
    foto_url VARCHAR(255) NULL
);

CREATE TABLE IF NOT EXISTS gebruikers (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    gebruikersnaam VARCHAR(50) NOT NULL UNIQUE,
    wachtwoord VARCHAR(255) NOT NULL
);
INSERT INTO `items` (`naam`, `rating`, `prijs`, `foto_url`) VALUES
('MSI Sword 16 HX Gaming Laptop (i7 / RTX 4060)', 4.7, 1249.00, 'https://www.megekko.nl/productimg/1119954/nw/1_MSI-Sword-16-HX-B14VFKG-015NL-Core-i7-RTX-4060-Gaming-laptop.jpg'),
('Logitech G Pro X Superlight Gaming Mouse Black', 4.9, 119.00, 'https://resource.logitechg.com/c_fill,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2c-pdp/gallery/pro-x-superlight-2c-mouse-top-angle-black-gallery-1.png'),
('Keychron K2 V2 Mechanical Keyboard (Brown Switches)', 4.5, 89.99, 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=500&auto=format&fit=crop&q=60'),
('ASUS TUF Gaming VG249Q1R Gaming Monitor (165Hz)', 4.6, 149.00, 'https://www.compustunt.nl//Files/2/30000/30591/ProductPhotos/Source/2007925155.jpg'),
('Sony WH-1000XM4 Wireless Noise Cancelling Headphones', 4.8, 229.00, 'https://www.sony.nl/image/5d02da5df552836db894cead8a68f5f3?fmt=png-alpha'),
('Samsung 990 PRO 2TB NVMe M.2 SSD', 4.9, 169.50, 'https://www.proshop.nl/Images/915x900/3103447_4ee041b928cb.png'),
('Corsair Vengeance RGB DDR5 32GB (2x16GB) 6000MHz', 4.7, 125.00, 'https://m.media-amazon.com/images/I/61EVf-QxpvL._AC_UF1000,1000_QL80_.jpg'),
('NZXT H5 Flow Black Mid Tower Case', 4.6, 94.90, 'https://www.proshop.nl/Images/915x900/3279765_131a79b9ae7a.jpg'),
('Be Quiet! Dark Rock Pro 5 CPU Cooler', 4.8, 89.90, 'https://tweakers.net/i/Im4pKL7gz7IdNGqqvZf7juYDTxE=/fit-in/374x326/filters:strip_icc():fill(white):strip_exif()/i/2006178906.jpeg?f=imagemediumplus');