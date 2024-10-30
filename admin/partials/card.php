<div>
    <div class="updatedpro mtb16 p0">
        <div class="card">
            <div class="card-header">
                <h3>Підтримка</h3>
            </div>
            <div class="card-body">
                <p>Якщо у вас виникли проблеми з плагіном,<br>звертайтесь до нашої підтримки в Facebook:</p>
                <h5><a href="https://www.facebook.com/groups/morkvasupport"
                    class="wpbtn button button-primary"
                    target="_blank"><?php echo '<img class="imginwpbtn" src="' . plugins_url('img/fbmessenger_logo.png', __FILE__) . '" />'; ?> Написати в чат</a></h5>

                <p>(Ваша поточна версія <?php echo MJS_PLUGIN_VERSION; ?>)
                <!-- <br> Можливо, в оновленій версії уже вирішена ваша проблема</p> -->
                <?php
                    $path = JUSTIN_PLUGFOLDER . '/public/partials/morkvajustin-plugin-invoices-page.php';
                    if ( ! file_exists( $path ) ) { ?>
                        <!-- <a href="plugin-install.php?tab=plugin-information&amp;plugin=justin-pro&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=374"
                            class="thickbox open-plugin-details-modal" >встановити останню версію плагіна</a> -->
                        <?php }
                    else{ ?>
                        <!-- <a href="plugin-install.php?tab=plugin-information&amp;plugin=justin-pro&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=374"
                            class="thickbox open-plugin-details-modal" >встановити останню версію плагіна</a> -->
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
        $path = JUSTIN_PLUGFOLDER . '/justin-pro.php';
        if ( ! file_exists( $path ) ) { ?>
            <div class="card">
                <div class="card-header">
                    <h3>Pro версія</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li style="margin-bottom:0;">1. Наложений платіж.</li>
                        <li style="margin-bottom:0;">2. Опис відправлення.</li>
                        <li style="margin-bottom:0;">3. Розрахунок габаритів і маси.</li>
                        <li style="margin-bottom:0;">4. Розрахунок вартості доставки.</li>
                        <li style="margin-bottom:0;">5. Пріорітетна підтримка.</li>
                    </ul>
                    Оновіться до Pro-версії зараз!
                    <h5><a href="https://morkva.co.ua/shop/woocommerce-justin" target="_blank" class="button button-primary">Хочу Pro</a></h5>
                </div>
            </div>

    <?php } ?>

    <div class="clear"></div>
</div>
