@extends('layout.app', ["current" => "produtos"])

@section ('body')

    <div class="card border dark">
        <div class="card-body">
            <h5 class="card-title">Cadastro de Produtos</h5>

            <table class="table table-ordered table-hover" id="tabelaProdutos">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Departamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-dark" role="button" onclick="novoProduto()">Novo produto</a>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="dlgProdutos">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="formProduto">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo produto</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" class="form-control">
                        <div class="form-group">
                            <label for="nomeProduto" class="control-label">Nome do Produto</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nomeProduto" placeholder="Nome do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="precoProduto" class="control-label">Preço</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="precoProduto" placeholder="Preço do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quantidadeProduto" class="control-label">Quantidade</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantidadeProduto" placeholder="Quantidade do produto">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="categoriaProduto" class="control-label">Categoria</label>
                            <div class="input-group">
                                <select class="form-control" id="categoriaProduto" >
                                </select>    
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-info">Salvar</button>
                        <button type="cancel" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('javascript')
    <script type="text/javascript">

        $.ajaxSetup({
            headers:  {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        
        function novoProduto()  {
            $('#id').val('');
            $('#nomeProduto').val('');
            $('#precoProduto').val('');
            $('#quantidadeProduto').val('');
            $('#dlgProdutos').modal('show');
            
        }

        function carregarCategorias()  {
            $.getJSON('/api/categorias', function(data)  { 
                for(i=0;i<data.length;i++)  {
                    opcao = '<option value ="' + data[i].id + '">' + 
                        data[i].nome + '</option>';
                    $('#categoriaProduto').append(opcao);
                }
            });
        }

        function montarLinha(produto)  {
            var linha = "<tr>" +
                "<td>" + produto.id + "</td>" +
                "<td>" + produto.nome + "</td>" +
                "<td>" + produto.estoque + "</td>" +
                "<td>" + produto.preco + "</td>" +
                "<td>" + produto.categoria_id + "</td>" +
                "<td>" +
                  '<button class="btn btn-sm btn-info" onclick="editarProdutos(' + produto.id + ')"> Editar </button> ' +
                  '<button class="btn btn-sm btn-danger" onclick="removerProdutos(' + produto.id + ')"> Apagar </button> ' +
                "</td>" +
                "</tr>";
            return linha;
        }

        function editarProdutos(id)  {
            $.getJSON('/api/produtos/'+id, function(data)  { 
                console.log(data);

                $('#id').val(data.id);
                $('#nomeProduto').val(data.nome);
                $('#precoProduto').val(data.preco);
                $('#quantidadeProduto').val(data.estoque);
                $('#categoriaProduto').val(data.categoria_id);
                $('#dlgProdutos').modal('show');
            });
        }

        function removerProdutos(id)  {
            $.ajax({
                type: "DELETE",
                url:  "/api/produtos/" + id,
                context: this,
                success: function ()  {
                    console.log('Apagou OK!');
                    $linhas = $("#tabelaProdutos>tbody>tr");
                    elemento = linhas.filter( function (i, elemento)  {
                        return elemento.cells[0].textContent == id;
                    });
                    if (elemento)
                        elemento.remove();
                },
                error: function (error)  {
                    console.log(error);
                }
            });
        }

        function carregarProdutos()  {
            $.getJSON('/api/produtos', function(produtos)  { 
                for(i=0;i<produtos.length;i++)  {
                    linha = montarLinha(produtos[i]);
                    $('#tabelaProdutos>tbody').append(linha);
                }
            });
        }

        function criarProduto()  {
            produto =  {
                nome: $("#nomeProduto").val(),
                preco: $("#precoProduto").val(),
                estoque: $("#quantidadeProduto").val(),
                categoria_id: $("#categoriaProduto").val()
            };
            $.post("/api/produtos", produto, function(data)  {
                prod =JSON.parse(data);
                linha = montarLinha(prod); 
                $('#tabelaProdutos>tbody').append(linha);
            });
        }

        function salvarProduto()  {
            produto =  {
                id: $("#id").val(),
                nome: $("#nomeProduto").val(),
                preco: $("#precoProduto").val(),
                estoque: $("#quantidadeProduto").val(),
                categoria_id: $("#categoriaProduto").val()
            };
            
            $.ajax({
                type: "PUT",
                url:  "/api/produtos/" + produto.id,
                context: this,
                data: produto,
                success: function(data)  {
                    produto = JSON.parse(data);
                    linhas = $("#tabelaProdutos>tbody>tr");
                    elemento = linhas.filter( function(indice, elemento)  {
                        return (elemento.cells[0].textContent == produto.id);
                    });

                    if (elemento)  {
                        elemento[0].cells[0].textContent = produto.id;
                        elemento[0].cells[1].textContent = produto.nome;
                        elemento[0].cells[2].textContent = produto.estoque;
                        elemento[0].cells[3].textContent = produto.preco;
                        elemento[0].cells[4].textContent = produto.categoria_id;
                    }
                },
                error: function (error)  {
                    console.log(error);
                }
            });

        }

        $("#formProduto").submit( function(event)  {
            event.preventDefault();
            if ($("#id").val() != '')
                salvarProduto(); 
            else
                criarProduto();
            $("dlgProdutos").modal('hide');
        });
        
        $(function()  {
            carregarCategorias();
            carregarProdutos(); 
        })
        
    </script>

@endsection

