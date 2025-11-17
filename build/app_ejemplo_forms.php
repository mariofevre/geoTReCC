<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        div {
            border: 1px solid black;
            margin: 10px;
        }

        a {
            border: 1px solid blue;
            margin: 3px;
        }

        #form1, #form2, #form3 {
            display: none;
        }

        #cuadrovalores[form1 = "si"] #form1, 
        #cuadrovalores[form2 = "si"] #form2,
        #cuadrovalores[form3 = "si"] #form3 {
            display: block;
        }
        

    </style>    
</head>

<body>
    <div id="pageborde">
        <div id="page">
            <div id="encabezado">Encabezado</div>
            <div id="modulos">Modulos</div>
            <div id="portamapa">Portamapa</div>
            <div id="cuadrovalores">
                <div id="accionesgenerales">
                    <a onclick="abre_form1();">1</a>
                    <a onclick="abre_form2();">2</a>
                    <a onclick="abre_form3();">3</a>
                </div>

                <div id="form1">Uno <a onclick="cerrarforms()">Cerrar</a></div>
                <div id="form2">Dos <a onclick="cerrarforms()">Cerrar</a></div>
                <div id="form3">Tres <a onclick="cerrarforms()">Cerrar</a></div>

            </div>

        </div>



    </div>

    <script type="text/javascript">
        function abre_form1(){
            document.querySelector("#cuadrovalores").setAttribute("form1", "si");
            document.querySelector("#cuadrovalores").setAttribute("form2", "no");
            document.querySelector("#cuadrovalores").setAttribute("form3", "no");
        }

        function abre_form2(){
            document.querySelector("#cuadrovalores").setAttribute("form1", "no");
            document.querySelector("#cuadrovalores").setAttribute("form2", "si");
            document.querySelector("#cuadrovalores").setAttribute("form3", "no");    
        }

        function abre_form3(){
            document.querySelector("#cuadrovalores").setAttribute("form1", "no");
            document.querySelector("#cuadrovalores").setAttribute("form2", "no");
            document.querySelector("#cuadrovalores").setAttribute("form3", "si");    
        }

        function cerrarforms(){
            document.querySelector("#cuadrovalores").setAttribute("form1", "no");
            document.querySelector("#cuadrovalores").setAttribute("form2", "no");
            document.querySelector("#cuadrovalores").setAttribute("form3", "no");
        }
    
    </script>
    
</body>
</html>