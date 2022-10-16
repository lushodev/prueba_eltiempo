CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT(11) NULL AUTO_INCREMENT , 
  `nombre` VARCHAR(100) NULL , 
  `marca` VARCHAR(100) NULL , 
  `categoria` VARCHAR(100) NULL , 
  `precio` INT(10) NULL , 
  `disponible` BOOLEAN NULL DEFAULT TRUE ,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;