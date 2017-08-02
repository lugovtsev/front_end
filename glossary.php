<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");

\Bitrix\Main\Loader::includeModule('highloadblock');
 $hlblock_id = 2;


use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock\HighloadBlockTable;

$hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);

$entity_data_class = $entity->getDataClass();
$entity_table_name = $hlblock['TABLE_NAME'];
$arFilter = (isset($_GET['is_premium'])) ? array("UF_PREMIUM"=>"1") : array();
$sTableID = 'tbl_'.$entity_table_name;
$rsData = $entity_data_class::getList(array(
"select" => array('*'), //выбираем все поля
"filter" => $arFilter,
"order" => array("UF_NAME"=>"ASC") // сортировка по полю UF_SORT, будет работать только, если вы завели такое поле в hl'блоке
));
$rsData = new CDBResult($rsData, $sTableID);
?>

  <section class="page-brands">
    <div class="container">
      <div class="row">
        <div class="col-xs-3">
          <p <?=(isset($_GET['is_premium'])) ? 'class = "gray"' : ''?>><a href="?all">Все бренды</a></p>
          <p <?=(!isset($_GET['is_premium'])) ? 'class = "gray"' : ''?>><a href="?is_premium">Premium</a></p>
        </div>
        <div class="col-xs-9">
          <h1>Бренды</h1>
          <?
            $arLinkSymbols = array("0-9");
            $arBrandGroups = array();
            while($arRes = $rsData->Fetch())
            {
              $linkSymbol = strtoupper(substr($arRes['UF_XML_ID'], 0, 1));
              //массив с символами-ссылками
              if (!preg_match('/[0-9а-яА-я]/', $linkSymbol) && !in_array($linkSymbol, $arLinkSymbols))
              {
                $arLinkSymbols[] = $linkSymbol;
              }
              // массив с брендами разбитый по группам (первым символам)
              if (preg_match('/[0-9]/', $linkSymbol))
              {
                $arBrandGroups["0-9"][] = $arRes['UF_XML_ID'];
              }
              else if (preg_match('/[а-яА-я]/', $linkSymbol))
              {
                $arBrandGroups["А-Я"][] = $arRes['UF_XML_ID'];
              }
              else
              {
                $arBrandGroups[$linkSymbol][] = $arRes['UF_XML_ID'];
              }
            }
            $arLinkSymbols[] = "А-Я";
            ?>
            <div class="link-symbols">
              <?
              foreach ($arLinkSymbols as $value)
              {
                ?>
                  <span><a href="#<?=$value?>"><?=$value?></a></span>
                <?
              }
              ?>
            </div>
            <?
            foreach ($arBrandGroups as $key => $value)
            {
              ?>
                <div class="brand-groups" id="<?=$key?>">
                  <h3><?=$key?></h3>
                  <div class="row">
              <?
              foreach ($value as $value2)
              {
                ?><div class="col-xs-4"><a href="/search/?q=<?=$value2?>"><?=$value2?></a></div><?
              }
              ?>
                  </div>
                </div>
              <?
            }
          ?>
        </div>
      </div>
    </div>
  </section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
