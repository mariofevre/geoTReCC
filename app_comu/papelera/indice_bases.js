var _Base={
	"I":{
		"I1":{
			"nombre":"aptitud para políticas de vivienda",
			"definicion":"Indicador de aptitud preliminara para recibir políticas de construcción y mejora de vivienda",
			"url":"./app_comu/papelera/I1_3857_p3.tif",
			"url_ovr":"./app_comu/papelera/I1_3857_p3.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"La zona indicada presenta bajas condiciones estadísticas para alojar políticas de vivienda"
					
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"La zona indicada presenta condiciones estadísticas medias para alojar políticas de vivienda. Te recomendamos analizar uales son las variables que no alcanzan el máximo valor."
				},
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"La zona indicada presenta altas condiciones estadísticas para alojar políticas de vivienda."
				}
			],
			"variables":[
				"A1",
				"A2",
				"B1",
				"B2",
				"C1",
				"C2",
				"C5",
				"D3"
			]
		}
	},

	"V":{
		"A1":{
			"nombre":"Densidad de establecimientos productivos",
			"definicion":"Variable de actividad productiva, indicando posibilida de empleo en la zona.",
			"url":"./bases/A1_3857_p1.tif",
			"url_ovr":"./bases/A1_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja presencia de estalbecimientos productivos. Puede indicar falta de oportunidades laborales."
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Presencia media de establecimentos productivos. Las condiciones de empleo pueden no resultar óptimas"
				},
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta presencia de establecimientos productivos. Las condiciones de empleo parecen óptimas."
				}
			],			
		},
		"A2":{
			"nombre":"Proximidad a agrupamientos industriales",
			"definicion":"Variable de actividad industrial, indicando posibilida de empleo en la zona.",
			"url":"./bases/A2_3857_p1.tif",
			"url_ovr":"./bases/A2_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja presencia de parques industriales. Puede indicar falta de oportunidades laborales.",				
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Presencia media de parques industriales. Las condiciones habiacionales pueden no resultar críticas",
				},
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta presencia de parques industriales. Las condiciones de empleo parecen óptimas.",
				}
			],
		},
		
		"B1":{
			"nombre": "Déficit de calidad de vivienda",
			"definicion":"Tipo de vivienda deficiente en una densidad mayor a 3 viv/ha. indicando precariedad habitacional.",
			"url":"./bases/B1_3857_p1.tif",
			"url_ovr":"./bases/B1_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja precariedad en tipos de vivienda. Puede indicar falta de demanda de proyectos habitacionles.",
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Precariedad habitacional media en tipos de vivienda. Las condiciones de empleo pueden no resultar óptimas",
				},				
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta precariedad habitacional en tipos de vivienda. Las condiciones de precatierad habitacional parecen ser críticas.",
				}
			
			],
		},
		"B2":{
			"nombre": "Déficit habitacionl simple",
			"definicion":"Cantidad de hogares en funcion de cantidad de viviendas. indicando precariedad habitacional.",
			"url":"./bases/B1_3857_p1.tif",
			"url_ovr":"./bases/B1_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja precariedad por déficit simple. Puede indicar falta de demanda de proyectos habitacionles.",
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Precariedad media por déficit simple. Las condiciones de empleo pueden no resultar óptimas",
				},				
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta precariedad por déficit simple. Las condiciones de precatierad habitacional parecen ser críticas.",
				}
			
			],
		},
		"C1":{
			"nombre": "Proximidad de escuelas",
			"definicion":"Densidad de escuelas.",
			"url":"./bases/C1_3857_p1.tif",
			"url_ovr":"./bases/C1_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja precariedad por déficit simple. Puede indicar falta de demanda de proyectos habitacionles.",
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Precariedad media por déficit simple. Las condiciones de empleo pueden no resultar óptimas",
				},				
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta precariedad por déficit simple. Las condiciones de precatierad habitacional parecen ser críticas.",
				}
			
			],
		},
		
		"C2":{
			"nombre": "Cobertura de servicios",
			"definicion":"Cobertura de servicios (agua, electricidad, gas).",
			"url":"./bases/C2_3857_p1.tif",
			"url_ovr":"./bases/C2_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja cobertura de servicios. Puede indicar que este suelo no es apto para alojar nuevas viviendas.",
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Cobertura media de servicios. Las coverturas de servicios pueden no resultar óptimas",
				},				
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta cobertura de servicios. Las condiciones de cobertura de servicios parecen ser óptimas.",
				}
			],
		},
			
		"C5":{
			"nombre": "Proximidad a espacios de esparcimiento (plazas y clubes)",
			"definicion":"Proximidad a espacios de esparcimiento valiosos para alojar tareas de cuidado.",
			"url":"./bases/C5_3857_p1.tif",
			"url_ovr":"./bases/C5_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Baja proximidad a espacios de esparcimiento. Prodría no resultar recomendable este sector.",
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Proximidad media a espacios de esparcimiento. Considere incorporar otros otros recursos para las tareas de cuidado.",
					
				},				
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"Alta proximidad a espacios de esparcimiento. Los sitios de esparcimiento parecen óptimos para facilitar las tareas de cuidado en la zona.",
				}
			
			],
		},
			
		"D3":{
			"nombre": "Zona no intervenida",
			"definicion":"Sin cercanía a obras o proyectos de vivienda pública.",
			"url":"./bases/D3_3857_p1.tif",
			"url_ovr":"./bases/D3_3857_p1.tif.ovr",
			"formula":"",
			"rangos":[
				{
					"min":0,
					"max":50,
					"eti":"bajo",
					"mg":"Ya existen obras de vivienda en la zona. Prodría no resultar recomendable este sector.",
				},
				{
					"min":50,
					"max":80,
					"eti":"media",
					"mg":"Ya existen obras de vivienda pública en la zona. Evaluar la persistencia de la demanda en la zona.",
					
				},				
				{
					"min":80,
					"max":100,
					"eti":"alt",
					"mg":"No registramos obras de vivienda pública en la zona. Las condiciones parecen adecuadas.",
				}
			]
		}
	}
}
