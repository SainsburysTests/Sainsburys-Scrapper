# Sainsburys-Scrapper

Sainsburys Product Scrapper to get started simply include the following:

```php
include "src/SainsburysScrapper.php";
$s = new SainsburysScrapper();
echo $s->fetch();
```

This should return a JSON encoded string;

```json

{"results":[{"title":"Sainsbury's Apricot Ripe & Ready x5","size":"38.27kb","unit_price":"3.50","description":"Apricots"},{"title":"Sainsbury's Avocado Ripe & Ready XL Loose 300g","size":"38.67kb","unit_price":"1.50","description":"Avocados"},{"title":"Sainsbury's Avocado, Ripe & Ready x2","size":"43.44kb","unit_price":"1.80","description":"Avocados"},{"title":"Sainsbury's Avocados, Ripe & Ready x4","size":"38.68kb","unit_price":"3.20","description":"Avocados"},{"title":"Sainsbury's Conference Pears, Ripe & Ready x4 (minimum)","size":"38.54kb","unit_price":"1.50","description":"Conference"},{"title":"Sainsbury's Golden Kiwi x4","size":"38.56kb","unit_price":"1.80","description":"Gold Kiwi"},{"title":"Sainsbury's Kiwi Fruit, Ripe & Ready x4","size":"38.98kb","unit_price":"1.80","description":"Kiwi"}],"total":"15.10"}
```
