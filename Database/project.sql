DROP DATABASE IF EXISTS project;
CREATE DATABASE project;
USE project;

CREATE TABLE adminAccount (
    adminID int NOT NULL auto_increment,
    username varchar(255) NOT NULL,
    adminFname varchar(255),
    adminLname varchar(255),
    password char(41) NOT NULL,
    adminEmail varchar(255),
    adminPhone varchar(12),
    gender enum('M','F'),
    DOB date,
    addressStreet varchar(255),
    city varchar(255),
    postcode varchar(255),
    states varchar(255),
    registerTime datetime NOT NULL,
    userlevel int,
    PRIMARY KEY (adminID)
);

INSERT INTO adminAccount (adminID, username, adminFname, adminLname, password, adminEmail, adminPhone, gender, DOB, addressStreet, city, postcode, states, registerTime, userlevel) VALUES
(1, 'Lily', 'Lily', 'Billy', MD5('Lily123'), 'lily@newera.edu.my', '018-9603217', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(2, 'Alex', 'Alex', 'Lee', MD5('Alex123'), 'alex@newera.edu.my', '017-6325864', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(3, 'Peter', 'Peter', 'Ong', MD5('Peter123'), 'peter@newera.edu.my', '012-3569873', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(4, 'Lucy', 'Lucy', 'Choong', MD5('Lucy123'), 'lucy@newera.edu.my', '013-6985941', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(5, 'Apple', 'Apple', 'Liew', MD5('Apple123'), 'apple@newera.edu.my', '019-2587638', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 2),
(6, 'zikeong', 'Zi Keong', 'Chong', MD5('keong123'), 'chongzikeong8155@e.newera.edu.my', '017-3982684', 'M', '1999-05-28', null, null, null, null, '2020-01-01 00:00:00', 1),
(7, 'Liew', 'Chun Lok', 'Liew', MD5('loklok'),'liewchunlok0267@e.newera.edu.my', '011-55506995', 'M', '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1);

CREATE TABLE userAccount (
    userID int NOT NULL auto_increment,
    username varchar(255) NOT NULL,
    userFname varchar(255),
    userLname varchar(255),
    password char(41) NOT NULL,
    userEmail varchar(255),
    userPhone varchar(12),
    gender enum('M','F'),
    DOB date,
    addressStreet varchar(255),
    city varchar(255),
    postcode varchar(255),
    states varchar(255),
    registerTime datetime NOT NULL,
    userlevel int,
    PRIMARY KEY (userID)
);

INSERT INTO userAccount (userID, username, userFname, userLname, password, userEmail, userPhone, gender, DOB, addressStreet, city, postcode, states, registerTime, userlevel) VALUES
(1, 'Alvin', 'Alvin', 'Chan', MD5('Alvin123'), 'alvin@newera.edu.my', '016-3987526', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(2, 'Danny', 'Danny', 'Low', MD5('Danny123'), 'danny@newera.edu.my', '012-6148684', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(3, 'Micky', 'Micky', 'Mouse', MD5('Micky123'), 'micky@newera.edu.my', '018-9623620', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(4, 'Orange', 'Orange', 'Tan', MD5('Orange123'), 'orange@newera.edu.my', '018-5823715', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1),
(5, 'Elaine', 'Elaine', 'Leong', MD5('Elaine123'), 'elaine@newera.edu.my', '017-9969996', null, '2000-01-01', null, null, null, null, '2020-01-01 00:00:00', 1);

CREATE TABLE category (
    categoryID int NOT NULL auto_increment,
    category varchar(255) NOT NULL,
    PRIMARY KEY (categoryID)
);

INSERT INTO category (categoryID, category) VALUES
(1, 'Laptop'),
(2, 'Keyboard'),
(3, 'Headset'),
(4, 'Mouse');

CREATE TABLE product (
    prod_ID int NOT NULL auto_increment,
    prod_name varchar(255) NOT NULL,
    prod_descr varchar(8000) NOT NULL,
    prod_image varchar(255),
    prod_price DOUBLE(12,2) NOT NULL,
    prod_category int NOT NULL,
    active int NOT NULL,
    PRIMARY KEY (prod_ID),
    FOREIGN KEY (prod_category) REFERENCES category(categoryID)
);

INSERT INTO product (prod_ID, prod_name, prod_descr, prod_image, prod_price, prod_category, active) VALUES
(1, 'ROG Strix Go 2.4 Electro Punk', 'ROG Strix Go 2.4 is a USB-C® 2.4 GHz wireless gaming headset equipped with an included 3.5 mm cable and an AI Noise-Canceling Microphone that provides low-latency performance across PC, Mac, Nintendo Switch™, PS5 and smart devices.', 'image/product/1.png', 799.00, 3, 1),
(2, 'ROG Delta White Edition', 'RGB gaming headset with Hi-Res ESS Quad-DAC, circular RGB lighting effect and USB-C connector for PCs, consoles and mobile gaming', 'image/product/2.png', 599.00, 3, 1),
(3, 'ROG Theta 7.1', 'USB-C gaming headset with 7.1 surround sound, AI noise-cancelling microphone, ROG home-theater-grade 7.1 DAC, ESS quad-drivers for PC, PS4, Nintendo Switch and smart devices', 'image/product/3.png', 1199.00, 3, 1),
(4, 'ROG Strix Go 2.4', 'ROG Strix Go 2.4 is a USB-C® 2.4 GHz wireless gaming headset equipped with an included 3.5 mm cable and an AI Noise-Canceling Microphone that provides low-latency performance across PC, Mac, Nintendo Switch™, PS5 and smart devices.', 'image/product/4.png', 699.00, 3, 1),
(5, 'ROG Delta', 'RGB gaming headset with Hi-Res ESS Quad-DAC, circular RGB lighting effect and USB-C connector for PCs, consoles and mobile gaming', 'image/product/5.png', 599.00, 3, 1),
(6, 'ROG Strix Fusion 700', 'PC, console and mobile gaming headset with Bluetooth 4.2, headset-to-headset RGB light synchronization, hi-fi-grade ESS DAC and amp, and 7.1 surround on the go', 'image/product/6.png', 899.00, 3, 1),
(7, 'ROG Strix Fusion 300 PNK LTD', 'ROG Strix Fusion 300 PNK LTD 7.1 gaming headset delivers immersive gaming audio and is compatible with PC, PS4, Xbox One and mobile devices', 'image/product/7.png', 599.00, 3, 1),
(8, 'ROG Cetra Core', 'In-ear gaming headphones with 10mm ASUS Essence drivers and 3.5mm connector for PC, PS4, Xbox One, mobile and Nintendo Switch', 'image/product/8.png', 499.00, 3, 1),
(9, 'ROG Strix Impact II Electro Punk', "ROG Strix Impact II Elctro Punk is a lightweight, ambidextrous gaming mouse that delivers smooth action and superb flexibility. It features pivoted buttons and a soft-rubber cable for fast, tactile clicks and unhindered glides. There's also a 6,200 dpi sensor for pinpoint accuracy.", 'image/product/9.png', 189.00, 4, 1),
(10, 'ROG Pugio', 'Optical wired gaming mouse with a truly ambidextrous design featuring configurable side buttons, exclusive push-fit switch socket design, and Aura RGB lighting with Aura Sync support', 'image/product/10.png', 269.00, 4, 1),
(11, 'ROG Strix Carry', 'ROG Strix Carry ergonomic optical gaming mouse with dual 2.4GHz/Bluetooth wireless connectivity, 7200-dpi sensor, and ROG-exclusive switch socket design', 'image/product/11.png', 249.00, 4, 1),
(12, 'ROG Strix Evolve', 'Optical gaming mouse with Aura Sync RGB lighting that features changeable top covers to enable four different ergonomic styles', 'image/product/12.png', 289.00, 4, 1),
(13, 'ROG Chakram', 'RGB wireless gaming mouse with Qi charging, programmable joystick, tri-mode connectivity (wired/2.4GHz/Bluetooth), advanced 16000 dpi sensor, screw-less magnetic design, and Aura Sync lighting', 'image/product/13.png', 629.00, 4, 1),
(14, 'ROG Gladius II', 'Ergonomic optical gaming mouse optimized for FPS gaming featuring easy-swap switch socket, Aura Sync RGB lighting and DPI target thumb button', 'image/product/14.png', 339.00, 4, 1),
(15, 'ROG Gladius II Origin PNK LTD', 'Ergonomic wired optical gaming mouse optimized for FPS, featuring Aura Sync', 'image/product/15.png', 399.00, 4, 1),
(16, 'ROG Spatha', 'Complete control for MMO victory.8200 DPI, 150 ips, 30g acceleration and 2000Hz USB polling rate supported in wired mode for pixel-precise mouse tracking', 'image/product/16.png', 679.00, 4, 1),
(17, 'ROG Strix Scope TKL Deluxe', 'ROG Strix Scope TKL Deluxe wired mechanical RGB gaming keyboard for FPS games, with Cherry MX switches, aluminum frame, ergonomic wrist rest, and Aura Sync lighting', 'image/product/17.jpg', 479.00, 2, 1),
(18, 'ROG Strix Flare PNK LTD', 'ROG Strix Flare PNK LTD RGB mechanical gaming keyboard with Cherry MX switches, customizable illuminated badge and dedicated media keys for gaming', 'image/product/18.png', 759.00, 2, 1),
(19, 'ROG Strix Flare', 'ROG Strix Flare RGB mechanical gaming keyboard with Cherry MX switches, customizable illuminated badge and dedicated media keys for gaming', 'image/product/19.png', 759.00, 2, 1),
(20, 'ROG Claymore', 'World’s first RGB mechanical gaming keyboard with a detachable numpad, Aura Sync and Cherry MX RGB switches', 'image/product/20.png', 549.00, 2, 1),
(21, 'ROG Claymore Core', 'RGB mechanical gaming keyboard with Cherry MX RGB switches, fully programmable keys and Aura Sync', 'image/product/21.png', 859.00, 2, 1),
(22, 'ROG Strix Scope', 'ROG Strix Scope RGB wired mechanical gaming keyboard with Cherry MX switches, aluminum frame, Aura Sync lighting and additional silver WASD for FPS games', 'image/product/22.png', 549.00, 2, 1),
(23, 'ROG Strix Scope Deluxe', 'ROG Strix Scope Deluxe RGB wired mechanical gaming keyboard with Cherry MX switches, aluminum frame, ergonomic wrist rest, Aura Sync lighting and additional silver WASD for FPS games', 'image/product/23.png', 599.00, 2, 1),
(24, 'ROG Strix Scope TKL Electro Punk', 'ROG Strix Scope TKL Electro Punk is a high-performance mechanical gaming keyboard with a small footprint, freeing up space on your worktop for\nder mouse movements – perfect for the lower sensitivity settings that slow the reticle for leveled-up aiming accuracy.', 'image/product/24.png', 479.00, 2, 1),
(25, 'ROG Mothership GZ700', 'Operating System: Windows 10 Home\nProcessor: Intel® Core™ i9-9980HK Processor 2.4 GHz(16M Cache,up to 5.0 GHz,8 cores)\nGraphics: NVIDIA® GeForce® RTX™ 2080 8GB GDDR6\nDisplay:17.3-inch,FHD(1920 x 1080) 16:9,anti-glare diplay\nMemory: 16GB DDR4-2666 SO-DIMM x 4,Memory max up to: 64GB\nCamera: FHD 1080P@30FPS\nBattery: 90WHrs, 3S2P,6-cell Li-ion', 'image/product/25.png', 26999.00, 1, 1),
(26, 'ROG Zephyrus G14 GA401', 'Operating System: Windows 10 Home\nProcessor: AMD Ryzen™ 7 4800HS Processor 2.9 GHz(8M Cache,up to 4.2 GHz)\nGraphics: NVIDIA® GeForce® GTX 1660Ti with Max-Q Design 6GB GDDR6\nDisplay: 14-inch, FHD(1920 x 1080)16:9,anti-glare diplay\nMemory: 8GB DDR4-3200 SO-DIMM, Memory max up to: 24GB\nCamera:  Optional\nBattery: 76WHrs, 4S1P,4-cell Li-ion', 'image/product/26.png', 4699.00, 1, 1),
(27, 'ROG Zephyrus S17 GX701', 'Operating System: Windows 10 Home\nProcessor: Intel® Core™ i7-10875H Processor 2.3 GHz(16M Cache,up to 5.1 GHz,8 cores)\nGraphics: NVIDIA® GeForce® RTX 2080 SUPER™ with Max-Q Design 8GB GDDR6\nDisplay: 17.3-inch, FHD(1920 x 1080)16:9,anti-glare diplay\nMemory: 16GB DDR4 on board,Memory max up to: 32GB\nCamera:  Optional\nBattery: 76WHrs, 4S1P,4-cell Li-ion', 'image/product/27.png', 13999.00, 1, 1),
(28, 'ROG Zephyrus Duo 15 GX550', 'Operating System: Windows 10 Pro\nProcessor: Intel® Core™ i9-10980HK Processor 2.4 GHz(16M Cache,up to 5.3 GHz,8 cores)\nGraphics: NVIDIA® GeForce® RTX 2080 SUPER™ with Max-Q Design 8GB GDDR6\nDisplay: 15.6-inch,4K UHD(3840 x 2160)16:9,anti-glare diplay\nMemory: 16GB DDR4 on board\nCamera:  Optional\nBattery: 90WHrs, 4S1P,4-cell Li-ion', 'image/product/28.png', 20009.00, 1, 1),
(29, 'ROG Zephyrus S15 GX502', 'Operating System: Windows 10 Home\nProcessor: Intel® Core™ i7-10875H Processor 2.3 GHz(16M Cache,up to 5.1 GHz,8 cores)\nGraphics: NVIDIA® GeForce® RTX 2080 SUPER™ with Max-Q Design 8GB GDDR6\nDisplay: 15.6-inch, FHD(1920 x 1080)16:9,anti-glare diplay\nMemory: 16GB DDR4 on board,Memory max up to: 32GB\nCamera:  Optional\nBattery: 76WHrs, 4S1P,4-cell Li-ion', 'image/product/29.png', 10499.00, 1, 1),
(30, 'ROG Strix G15 G512', 'Operating System: Windows 10 Home\nProcessor: Intel® Core™ i5-10300H Processor 2.5 GHz(8M Cache,up to 4.5 GHz,4 cores)\nGraphics: NVIDIA® GeForce® GTX 1650 Ti 4GB GDDR6\nDisplay: 15.6-inch, FHD(1920 x 1080)16:9,anti-glare diplay\nMemory: 8GB DDR4 SO-DIMM,Memory max up to: 32GB\nCamera:  Optional\nBattery: 48WHrs, 3S1P,3-cell Li-ion', 'image/product/30.png', 3999.00, 1, 1),
(31, 'ROG Strix SCAR 15 G532', 'Operating System: Windows 10 Home\nProcessor: Intel® Core™ i7-10875H Processor 2.3 GHz(16M Cache,up to 5.1 GHz,8 cores)\nGraphics: NVIDIA® GeForce RTX™ 2060 6GB GDDR6\nDisplay: 15.6-inch, FHD(1920 x 1080)16:9,anti-glare diplay\nMemory: 8GB DDR4 SO-DIMM,Memory max up to: 32GB\nCamera:  Optional\nBattery: 66WHrs, 4S1P,4-cell Li-ion', 'image/product/31.png', 7499.00, 1, 1),
(32, 'ROG Strix Hero III G531', 'Operating System: Windows 10 Home\nProcessor: Intel® Core™ i7-9750H Processor 2.6 GHz(12M Cache,up to 4.5 GHz,6 cores)\nGraphics: NVIDIA® GeForce RTX™ 2060 6GB GDDR6\nDisplay: 15.6-inch, FHD(1920 x 1080)16:9,anti-glare diplay\nMemory: 16GB DDR4-2666 SO-DIMM\nCamera:  Optional\nBattery: 66WHrs, 4S1P,4-cell Li-ion', 'image/product/32.png', 6599.00, 1, 1);

CREATE TABLE cart (
    cartID int NOT NULL auto_increment,
    username varchar(255) UNIQUE NOT NULL,
    cart varchar(8000),
    PRIMARY KEY (cartID)
);

CREATE TABLE orderstatuslist (
    statusID int NOT NULL,
    statusdetails varchar(255) NOT NULL,
    PRIMARY KEY (statusID)
);

INSERT INTO orderstatuslist (statusID, statusdetails) VALUES
(0, 'Cancelled'),
(1, 'Proccessing'),
(2, 'On Delivery'),
(3, 'Completed');

CREATE TABLE orderlist (
    orderID int NOT NULL auto_increment,
    username varchar(255) NOT NULL,
    shipaddress varchar(8000) NOT NULL,
    postcode int(5) NOT NULL,
    shipmethod varchar(255) NOT NULL,
    shipfee DOUBLE(5,2) NOT NULL,
    paymentmethod varchar(255) NOT NULL,
    orderdate datetime NOT NULL,
    shipdate date NOT NULL,
    statusID int NOT NULL,
    PRIMARY KEY (orderID),
    FOREIGN KEY (statusID) REFERENCES orderstatuslist(statusID)
);

CREATE TABLE orderitem (
    orderID int NOT NULL auto_increment,
    prod_ID int NOT NULL,
    prod_name varchar(255) NOT NULL,
    qty int NOT NULL,
    listprice DOUBLE(100,2) NOT NULL,
    FOREIGN KEY (prod_ID) REFERENCES product(prod_ID),
    FOREIGN KEY (orderID) REFERENCES orderlist(orderID)
);