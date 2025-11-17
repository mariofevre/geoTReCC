/*
 * 
 *  definicion de variables y funciones de recorte para mapas en todos los módulos
 * */



function generarBotonDescarga(){
	
	_div=document.createElement('div');
	_div.setAttribute('id','menudescarga');
	_div.setAttribute('abierto','-1');
	document.querySelector('#page #portamapa #botonera_mapa').appendChild(_div);
	
	_bc=document.createElement('a');
	_bc.setAttribute('id','botondescarga');	
	_bc.innerHTML='<img src="./img/descargar.png"></a>'
	
	_bc.setAttribute('onclick','descargaMapa()');
	_div.appendChild(_bc);
	
	
	
	
}
generarBotonDescarga();



function descargaMapa(){
	
	_canvas=document.querySelector('#portamapa #mapa canvas');
		
	 const mapCanvas = document.createElement('canvas');
		const size = mapa.getSize();
		mapCanvas.width = size[0];
		mapCanvas.height = size[1];
		const mapContext = mapCanvas.getContext('2d');
		Array.prototype.forEach.call(
		  document.querySelectorAll('.ol-layer canvas'),
		  function (canvas) {
			if (canvas.width > 0) {
			  const opacity = canvas.parentNode.style.opacity;
			  mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
			  const transform = canvas.style.transform;
			  // Get the transform parameters from the style's transform matrix
			  const matrix = transform
				.match(/^matrix\(([^\(]*)\)$/)[1]
				.split(',')
				.map(Number);
			  // Apply the transform to the export map context
			  CanvasRenderingContext2D.prototype.setTransform.apply(
				mapContext,
				matrix
			  );
			  mapContext.drawImage(canvas, 0, 0);
			}
		  }
		);
		_nombre='mapa';
		_f=new Date();    
		_filename=_nombre+'_'+_f.getFullYear()+'_'+_f.getMonth()+'_'+_f.getDate()+'.png';
		var link = document.createElement('a');
		link.download = _filename;
		link.href=mapCanvas.toDataURL();
		link.click();

}
