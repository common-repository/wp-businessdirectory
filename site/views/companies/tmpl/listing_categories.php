 <!-- Business Categories -->
 <?php if(!empty($this->company->categoriesDetails)){?>
    <?php if($this->appSettings->listing_category_display == 2){?>
        <?php foreach($this->company->categoriesDetails as $categ){?>
        <?php $cat = $categ["cat"]; ?>
            <div>
            <a href="<?php echo JBusinessUtil::getCategoryLink($cat->id, $cat->alias) ?>"><?php echo $this->escape($cat->name) ?></a>
            <?php if(!empty($categ["subcategories"])){?>
                &nbsp;&gt;&nbsp;
                <?php $index = 0;?>
                <?php foreach($categ["subcategories"] as $catData){ $index++?>
                    <?php $cat = $catData["cat"];?>
                    <a href="<?php echo JBusinessUtil::getCategoryLink($cat->id, $cat->alias) ?>"><?php echo $this->escape($cat->name) ?></a><?php echo $index<count($categ["subcategories"]) && empty($catData["subcategories"])?", ":""?>
                    <?php if(!empty($catData["subcategories"])){ echo ",&nbsp;"; ?>
                        <?php $index2 = 0;?>
                        <?php foreach($catData["subcategories"] as $catInfo){ $index2++?>
                            <?php $cat = $catInfo["cat"];?>
                            <a href="<?php echo JBusinessUtil::getCategoryLink($cat->id, $cat->alias) ?>"><?php echo $this->escape($cat->name) ?></a><?php echo $index2<count($catData["subcategories"]) && empty($catInfo["subcategories"])?", ":""?>
                             <?php if(!empty($catInfo["subcategories"])){?>,&nbsp;
                                <?php $index3 = 0;?>
                                <?php foreach($catInfo["subcategories"] as $catInf){ $index3++?>
                                    <?php $cat = $catInf["cat"];?>
                                    <a href="<?php echo JBusinessUtil::getCategoryLink($cat->id, $cat->alias) ?>"><?php echo $this->escape($cat->name) ?><?php echo $index3<count($catInfo["subcategories"])?", ":""?></a>
                                <?php } ?>
                                <?php echo $index2<count($catData["subcategories"])?", ":""?>
                             <?php } ?>
                         <?php } ?>
                         <?php echo $index<count($categ["subcategories"])?", ":""?>
                     <?php } ?>
                <?php } ?>
            <?php } ?>
            </div>
        <?php } ?>
    <?php }else{ ?>
         <?php foreach($this->company->categoriesDetails as $i=>$category){ ?>
             <a href="<?php echo JBusinessUtil::getCategoryLink($category[0], $category[2]) ?>"><?php echo $this->escape($category[1]) ?></a><?php echo $i<(count($this->company->categories)-1)? ',&nbsp;':'' ?>
         <?php } ?>
    <?php } ?>
<?php } ?>
