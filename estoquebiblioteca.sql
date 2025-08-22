create table fornecedor(

    idfornecedor int not null auto_increment,
    nome varchar(40),
    nomefantasia varchar(60),
    CNPJ varchar(20),
    primary key (idfornecedor)

);

create table produto(

    idproduto int not null auto_increment,
    idfornecedor int,
    nome varchar(40),
    descricao varchar(256),
    genero varchar(1),
    quantidade int,
    preco text,
    primary key (idproduto),
    FOREIGN KEY (idfornecedor) REFERENCES fornecedor(idfornecedor)

);

create table funcionario(

    idfuncionario int not null auto_increment,
    nome varchar(40),
    telefone text,
    email varchar(40),
    datanascimento text,
    primary key (idfuncionario)   

);

create table cliente(

 idcliente int not null auto_increment,
 NomeCliente text,
 CNPJ varchar(20),
 primary key (idcliente)

);
ALTER TABLE produto ADD FOREIGN KEY (idfornecedor) REFERENCES fornecedor(idfornecedor);

create table livro (
	idlivro int AUTO_INCREMENT,
    titulo varchar(255),
    autor varchar (200),
    editora varchar(150),
    ano_publicacao int(4),
    edicao varchar(20),
    idioma varchar(50),
    primary key (idlivro)
);