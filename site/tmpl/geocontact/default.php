<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

//$document = Factory::getDocument();
//$document->setTitle("Сеть продаж: теплицы из поликарбоната в " . $this->caption_morph5 );

?>

<article>

</article>

<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1>
			<?php if ($this->escape($this->params->get('page_heading'))) : ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
        </h1>
    </div>
<?php endif; ?>
<div class="table-responsive">
    <table class="table table-striped">
        <tr>
			<th class="item-description">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_DESCRIPTION'); ?>
			</th>
			<td>
				<?php echo $this->item->description; ?>
			</td>
		</tr>
		<tr>
			<th class="item-stand">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_STAND'); ?>
			</th>
			<td>
				<?php echo $this->item->stand; ?>
			</td>
		</tr>
		<tr>
			<th class="item-address">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_ADDRESS'); ?>
			</th>
			<td>
				<?php echo $this->item->address; ?>
			</td>
		</tr>
		<tr>
			<th class="item-name">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_NAME'); ?>
			</th>
			<td>
				<?php echo $this->item->name; ?>
			</td>
		</tr>
		<tr>
			<th class="item-phones">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_PHONES'); ?>
			</th>
			<td>
				<?php echo $this->item->phones; ?>
			</td>
		</tr>
		<tr>
			<th class="item-latlong">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_LATLONG'); ?>
			</th>
			<td>
				<?php echo $this->item->latlong; ?>
			</td>
		</tr>
		<tr>
			<th class="item-caption">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_CAPTION'); ?>
			</th>
			<td>
				<?php echo $this->item->caption; ?>
			</td>
		</tr>
		<tr>
			<th class="item-created_by">
				<?php echo Text::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_GEOCONTACT_CREATED_BY'); ?>
			</th>
			<td>
				<?php echo $this->item->created_by; ?>
			</td>
		</tr>
    </table>
</div>



<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <div id="ymap" style="width: 100%; height: 400px"></div>
            <script type="text/javascript">

                ymaps.ready(function () {
                    var myMap = window.map = new ymaps.Map('ymap', {
                        center: [<?php echo $this->item->latlong; ?>],
                        zoom: 14,
                        behaviors: ['default', 'scrollZoom'],
                        type: 'yandex#map'
                    });

                    /**
                     * Создадим кластеризатор, вызвав функцию-конструктор.
                     * Список всех опций доступен в документации.
                     * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Clusterer.xml#constructor-summary
                     */

                    var clusterer = new ymaps.Clusterer({
                            /**
                             * Через кластеризатор можно указать только стили кластеров,
                             * стили для меток нужно назначать каждой метке отдельно.
                             * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/option.presetStorage.xml
                             */
                            preset: 'twirl#invertedRedClusterIcons',
                            /**
                             * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
                             */
                            groupByCoordinates: false,
                            /**
                             * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
                             * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Cluster.xml
                             */
                            clusterDisableClickZoom: true
                        }),
                        /**
                         * Функция возвращает объект-данных для метки.
                         * Поле данных clusterCaption будет отображено в списке геообъектов в балуне кластера.
                         * Поле balloonContentBody - источник данных для контента балуна.
                         * Оба поля поддерживают HTML-разметку.
                         * Список полей данных, которые используют стандартные макеты содержимого иконки метки
                         * и балуна геообъектов, можно посмотреть в документации.
                         * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/GeoObject.xml
                         */
                        getPointData = function (phone, caption) {
                            return {
                                balloonContentBody: 'тел.&nbsp;<strong>' + phone + '</strong>',
                                clusterCaption: caption
                            };
                        },
                        /**
                         * Функция возвращает объект-опций для метки.
                         * Все опции, которые поддерживают геообъекты можно посмотреть в документации.
                         * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/GeoObject.xml
                         */
                        getPointOptions = function () {
                            return {
                                // Опции.
                                // Необходимо указать данный тип макета.
                                iconLayout: 'default#image',
                                // Своё изображение иконки метки.
                                iconImageHref: '/components/com_geocontact/assets/images/marker.png',
                                // Размеры метки.
                                iconImageSize: [44, 48],
                                // Смещение левого верхнего угла иконки относительно
                                // её "ножки" (точки привязки).
                                iconImageOffset: [-18, -48]
                            };
                        },
                        sellpoints = [
                            <?php
                            $active = "55.022287, 36.479229";
                            foreach ($this->towns as $town) {
                                echo "{p: [" . $town->latlong . "], phone: '" . $town->phones . "', caption: '" . $town->caption . "'}" . (",") . "\n";
                                if ($town->caption == $this->item->caption) {
                                    $active = $town->latlong;
                                }
                            }
                            ?>
                        ],
                        geoObjects = [];

                    /**
                     * Данные передаются вторым параметром в конструктор метки, опции третьим.
                     * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Placemark.xml#constructor-summary
                     */
                    for (var i = 0, len = sellpoints.length; i < len; i++) {
                        geoObjects[i] = new ymaps.Placemark(sellpoints[i].p, getPointData(sellpoints[i].phone, sellpoints[i].caption), getPointOptions());
                        /**
                         * Так же их можно добавлять/менять динамически после создания меток.
                         * geoObjects[i].properties.set(getPointData(i));
                         * geoObjects[i].options.set(getPointOptions());
                         */
                    }

                    /**
                     * Так же можно менять опции кластеризатора.
                     */
                    clusterer.options.set({
                        gridSize: 80,
                        clusterDisableClickZoom: true
                    });

                    /**
                     * В кластеризатор можно добавить javascript-массив меток (не геоколлекцию) или одну метку.
                     * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Clusterer.xml#add
                     */
                    clusterer.add(geoObjects);

                    /**
                     * Поскольку кластеры добавляются асинхронно,
                     * дождемся их добавления, чтобы выставить карте область, которую они занимают.
                     * Используем метод once чтобы сделать это один раз.
                     */
                    clusterer.events.once('objectsaddtomap', function () {
                        myMap.setBounds(clusterer.getBounds(), {
                                checkZoomRange: true
                            }
                        );
                    });

                    /**
                     * Кластеризатор, расширяет коллекцию, что позволяет использовать один обработчик
                     * для обработки событий всех геообъектов.
                     * Выведем текущий гееобъект, на который навели курсор, поверх остальных.
                     */
                    clusterer.events
                        // Можно слушать сразу несколько событий, указывая их имена в массиве.
                        .add(['mouseenter', 'mouseleave'], function (e) {
                            var target = e.get('target'), // Геообъект - источник события.
                                eType = e.get('type'), // Тип события.
                                zIndex = Number(eType === 'mouseenter') * 1000; // 1000 или 0 в зависимости от типа события.

                            target.options.set('zIndex', zIndex);
                        });

                    /**
                     * После добавления массива геообъектов в кластеризатор,
                     * работать с геообъектами можно, имея ссылку на этот массив.
                     */
                    clusterer.events.add('objectsaddtomap', function () {
                        for (var i = 0, len = geoObjects.length; i < len; i++) {
                            var geoObject = geoObjects[i],
                                /**
                                 * Информацию о текущем состоянии геообъекта, добавленного в кластеризатор,
                                 * а также ссылку на кластер, в который добавлен геообъект, можно получить с помощью метода getObjectState.
                                 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Clusterer.xml#getObjectState
                                 */
                                geoObjectState = clusterer.getObjectState(geoObject),
                                // признак, указывающий, находится ли объект в видимой области карты
                                isShown = geoObjectState.isShown,
                                // признак, указывающий, попал ли объект в состав кластера
                                isClustered = geoObjectState.isClustered,
                                // ссылка на кластер, в который добавлен объект
                                cluster = geoObjectState.cluster;

                            if (window.console) {
                                console.log('Геообъект: %s, находится в видимой области карты: %s, в составе кластера: %s', i, isShown, isClustered);
                            }
                        }
                    });

                    myMap.geoObjects.add(clusterer);
                });

            </script>
            <style type="text/css">
                #ymap {
                    width: 100%;
                    height: 500px;
                }
            </style>

        </div>
        <div class="col-sm-6">
            <?php echo $this->item->description; ?>
            <a class="btn btn-white more" href="/company-info/contact-us">
                Заказать<i class="pl-10 fa fa-shopping-cart"></i>
            </a>
        </div>
    </div>
</div>
<footer class="page-footer"></footer>

<?php
//print_r($this->towns);
//print_r($this->item);
?>

