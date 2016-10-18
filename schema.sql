--
-- Structure de la table `clientes`
--

CREATE TABLE `clientes` (
  `uuid` varchar(36) NOT NULL,
  `host` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senhamysql` varchar(40) NOT NULL,
  `senharoot` varchar(40) NOT NULL,
  `senhaftp` varchar(40) NOT NULL,
  `senhasenha` varchar(40) NOT NULL,
  `plano` enum('planoum','planodois', 'planotres') NOT NULL,
  `pago` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- O password é um SHA1 da palavra 'password'.
INSERT INTO clientes (uuid,host, email, senhamysql, senharoot, senhaftp, senhasenha, plano)
     VALUES (uuid(),
             'tche.com.br', 
     	     'tche@tche.com.br', 
     	     '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',
     	     '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',
     	     '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',
     	     '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 
     	     'planotres');