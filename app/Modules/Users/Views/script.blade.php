<script>
    (()=>{
        
        $('input[name="parameters[2][5]"]').on('blur', function() {
            const minimalYear = 16;
            const selectedDate = $(this).val();
            const currentDate = new Date();

            const selectedMs = new Date(selectedDate).getTime();
            const currentMs = currentDate.getTime();
            
            const differenceMS = currentMs - selectedMs;
            
            const differenceYears = differenceMS / (1000 * 60 * 60 * 24 * 365);
            
            if (differenceYears < minimalYear) {
                swalRunnable(`A forLEARN® detectou que com essa data de nascimento ainda não tens pelo menos ${minimalYear} anos. Por favor verifique o ano de nascimento`);
            }
            
        });
        
        const changeDataEmmissao = (x,y) => {
            $(x).on('change', function() {
                const selectedDateText = $(this).val();
                const selectedDate = new Date(selectedDateText);
                selectedDate.setFullYear(selectedDate.getFullYear() + 10);
                const newDate = selectedDate.toISOString().split('T')[0];
                $(y).val(newDate);
            });
        }
        
        changeDataEmmissao('input[name="parameters[3][15]"]', 'input[name="parameters[3][16]"]');
        
        changeDataEmmissao('input[name="parameters[13][15]"]', 'input[name="parameters[13][16]"]');
            
    })();  
</script>