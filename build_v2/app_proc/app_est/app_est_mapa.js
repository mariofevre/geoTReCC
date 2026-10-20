/**

*
* aplicación para generar un mapa on line incorporando variable de la base de datos
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/
//funciones para el control del mapa


 
mapa.on('pointermove', function(evt) {
	
    if (evt.dragging) {
    	
    	//console.log(evt);
    	//deltaX = evt.coordinate[0] - evt.coordinate_[0];
		//deltaY = evt.coordinate[1] - evt.coordinate_[1];
		//console.log(deltaX);
		
      return;
    }
    var pixel = mapa.getEventPixel(evt.originalEvent);

    sobrePunto(pixel);
});

mapa.on('click', function(evt){    	
  consultaPunto(evt.pixel,evt);       
});

var sobrePunto = function(pixel) {    
    	
		//if(_Dibujando=='si'){return;}	
    	
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
        	//console.log(layer.get('name'));
	        if(layer.get('name')=='seleccionLayer'){	        	
	          return feature;
	        }else{
	        	console.log('no');
	        }
        });
       
   	
        if(feature==undefined){
        	
        	_features = _sourceSeleccion.getFeatures();
        	for(_nn in _features){        		
        		_features[_nn].setStyle(_CentStyle);        		
	    		document.querySelector('#muestra').innerHTML='';
	    		
    		}
    		return;
        }
        
        feature.setStyle(_CentSelStyle);
        //console.log(feature.getProperties('nom'));
        _pp=feature.getProperties('nom');
		document.querySelector('#muestra').innerHTML=_pp.nom;
		document.querySelector('#muestra').style.display='inline-block';
	   
    }
    
   function consultaPunto(pixel,_ev){
		
	    if(_MapaCargado=='no'){return;}
		var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
			
	       if(layer.get('name')=='seleccionLayer'){
	          return feature;
	       }
	    });
	    //console.log(feature);
	     if(feature==undefined){return;}
	     	 
		_idreg=feature.getProperties().id;
		
		_datasec=_Features[_idreg];
		
		_aux=document.querySelector('#auxiliar');	
		if(_aux.getAttribute('mostrando')==_idreg){return;}
		
		 _pp=feature.getProperties();
		//console.log(_pp);
	
		consultarGeometria(_pp.tabla,_pp.cod);
		
		
	}
    