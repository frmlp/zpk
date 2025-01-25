import L from 'leaflet';
import $ from 'jquery';
import proj4 from 'proj4';
global.proj4 = proj4;
const helpersJS = require('../../public/js/helpers');
global.transformToWSG84 = helpersJS.transformToWSG84;
const mapJS = require('../../public/js/map');

describe('Leaflet Map Integration Test with jQuery AJAX', () => {
  let map;
  let points = [];
  let markers = [];


  beforeEach(() => {
    document.body.innerHTML = '<div id="map" style="height: 400px;"></div>';
    map = mapJS.initMap('map');
    markers = mapJS.initMarkers();

    // Mockowanie odpowiedzi backendu
    $.ajax = jest.fn().mockImplementation(({ success }) => {
      success([
        {"id":1,"code":"31","description":"kamień","easting":467854.23,"northing":739890.04,"pointVirtual":0,"url":"http://bit.ly/iOrien?q=QkNPfDJ8MzE=","areas":[{"id":1,"name":"Grabówek"}],"tags":[{"id":16,"name":"Mrowisko"},{"id":60,"name":"Rurociąg"}]},
        {"id":2,"code":"32","description":"rozwidlenie drogi i ścieżki","easting":468014.71,"northing":739591.07,"pointVirtual":0,"url":"http://bit.ly/iOrien?q=QkNPfDJ8MzI=","areas":[{"id":1,"name":"Grabówek"}],"tags":[{"id":8,"name":"Suchy rów"},{"id":55,"name":"Płot"}]}
      ]);
    });
  });

  test('should render markers based on backend data using jQuery AJAX', (done) => {
    $.ajax({
      url: '/api/points',
      method: 'GET',
      success: (result) => {
        points = result;

        points.forEach(point => {
          point.popup = point.code + " - " + point.description;
        });

        mapJS.initPointsPreview2(points, markers, map);

        let layerCount = 0;
        map.eachLayer(() => {
          layerCount++;
        });

        // sprawdzamy czy mapa ma wszystkie warstwy: tiles, attribution, markers
        expect(layerCount).toBe(2 + points.length); // trzy wartswy dla, bo dodawany jest także tile layer i attribution layer
        done();
      }
    });
  });
});