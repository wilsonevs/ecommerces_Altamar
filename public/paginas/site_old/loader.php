<div class="loader">
  <div id="particles-background" class="vertical-centered-box"></div>
  <div id="particles-foreground" class="vertical-centered-box"></div>

  <div class="vertical-centered-box">
    <div class="content-loader">
      <div class="loader-circle"></div>
      <div class="loader-line-mask">
        <div class="loader-line"></div>
      </div>
      <!-- <svg width="36px" height="24px" viewBox="0 0 36 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path d="M8.98885921,23.8523026 C8.8942483,23.9435442 8.76801031,24 8.62933774,24 L2.03198365,24 C1.73814918,24 1.5,23.7482301 1.5,23.4380086 C1.5,23.2831829 1.55946972,23.1428989 1.65570253,23.0416777 L13.2166154,12.4291351 C13.3325814,12.3262031 13.4061076,12.1719477 13.4061076,11.999444 C13.4061076,11.8363496 13.3401502,11.6897927 13.2352673,11.587431 L1.68841087,0.990000249 C1.57298556,0.88706828 1.5,0.733668282 1.5,0.561734827 C1.5,0.251798399 1.73814918,2.85130108e-05 2.03198365,2.85130108e-05 L8.62933774,2.85130108e-05 C8.76855094,2.85130108e-05 8.89532956,0.0561991444 8.98994048,0.148296169 L21.4358709,11.5757407 C21.548593,11.6783875 21.6196864,11.8297916 21.6196864,11.999444 C21.6196864,12.1693815 21.5483227,12.3219261 21.4350599,12.4251432 L8.98885921,23.8523026 Z M26.5774333,23.8384453 L20.1765996,17.9616286 C20.060093,17.8578413 19.9865669,17.703871 19.9865669,17.5310822 C19.9865669,17.3859509 20.0390083,17.2536506 20.1246988,17.153855 L23.4190508,14.1291948 C23.5163648,14.0165684 23.6569296,13.945571 23.8131728,13.945571 C23.9602252,13.945571 24.0929508,14.0082997 24.1894539,14.1092357 L33.861933,22.9913237 C33.9892522,23.0939706 34.0714286,23.2559245 34.0714286,23.4381226 C34.0714286,23.748059 33.8332794,23.9998289 33.5394449,23.9998289 L26.9504707,23.9998289 C26.8053105,23.9998289 26.6733958,23.9382408 26.5774333,23.8384453 Z M26.5774333,0.161098511 C26.6733958,0.0615881034 26.8053105,0 26.9504707,0 L33.5394449,0 C33.8332794,0 34.0714286,0.251769886 34.0714286,0.561706314 C34.0714286,0.743904453 33.9892522,0.905573224 33.861933,1.00822006 L24.1894539,9.89030807 C24.0929508,9.99152926 23.9602252,10.0542579 23.8131728,10.0542579 C23.6569296,10.0542579 23.5163648,9.98354562 23.4190508,9.87063409 L20.1246988,6.8459739 C20.0390083,6.74617837 19.9865669,6.613878 19.9865669,6.46874677 C19.9865669,6.29624305 20.060093,6.14198767 20.1765996,6.03848544 L26.5774333,0.161098511 Z" fill="#FFFFFF"></path>
      </svg> -->

      <svg width="50" height="50" xmlns="http://www.w3.org/2000/svg">
       <g>
        <rect fill="none" id="canvas_background" height="52" width="52" y="-1" x="-1"/>
        <g display="none" overflow="visible" y="0" x="0" height="100%" width="100%" id="canvasGrid">
         <rect fill="url(#gridpattern)" stroke-width="0" y="0" x="0" height="100%" width="100%"/>
        </g>
       </g>
       <g>
        <path stroke="#ffffff" id="svg_2" d="m22.447219,23.693447c0.154829,0.107681 0.482488,-0.096264 0.641674,-0.121464c-0.179167,-0.244765 0.495689,-1.437214 0.262773,-1.87026c-0.071667,-0.979059 -1.885706,-3.189083 -2.114165,-3.445536c0.087147,-0.171983 1.063791,-0.011945 1.034637,0.012728c-0.407554,0.345005 1.375297,2.32501 1.581195,3.470465c0.379389,-0.022066 0.87773,-0.171441 1.098893,0.075312c0.206529,-0.237158 1.800093,-2.049202 1.887211,-3.614989c0.012484,-0.224385 0.877386,-0.082377 0.714688,0.054391c-0.260795,0.974872 -2.099889,3.711991 -2.243566,3.811644c0.167213,0.853529 0.099516,0.912032 1.167564,1.575713c0.480668,-1.186093 1.504827,-2.192912 2.442857,-3.194987c1.05919,-1.125135 1.770036,-2.203004 2.936797,-3.374276c1.535945,-1.487854 3.497517,-2.845463 5.294084,-3.655883c1.249794,-0.563773 3.343806,-1.047081 3.664816,-1.078713c0.59469,-0.060672 1.132476,-0.049902 1.837374,0.060159c1.040098,0.162388 2.055671,0.253079 3.147669,0.791743c1.026796,0.691336 0.671846,1.557789 0.480396,2.369384c-1.581438,1.832694 -3.201176,4.027595 -3.711299,6.27309c-0.171513,0.831462 -0.479894,2.264233 -1.207711,3.16423c-1.170086,1.384375 -2.6021,1.333178 -3.947569,1.520841c0.819823,0.246678 1.709005,1.052127 2.506726,1.861508c1.244405,1.273063 1.850762,2.578602 1.529409,3.99991c-0.068413,0.298116 -1.254925,2.559292 -1.835267,3.268793c-2.202716,2.701647 -5.201534,4.533859 -7.618519,4.236496c-0.870391,-0.107079 -3.271223,-2.027542 -3.702871,-2.594221c-1.071545,-1.406743 -1.951697,-3.103543 -1.987874,-4.584966c-0.1332,-0.139855 -0.296098,-0.150188 -0.224273,-0.337294c-0.133472,0.428451 -0.557308,1.823747 -1.184778,2.705834c-0.907142,1.275248 -1.737528,-0.631057 -2.073645,-2.176991c-0.115226,-0.529942 -0.030659,-0.621688 -0.165048,-0.938572c-0.635468,0.253381 -0.300871,0.988067 -0.550256,1.581558c-0.211431,0.516823 -1.158304,2.095398 -1.419787,2.45527c-0.835848,1.15035 -2.712682,2.891102 -4.073817,3.308799c-1.263296,0.387677 -2.333995,0.333287 -3.349296,-0.213947c-1.309564,-0.705827 -3.155123,-2.345088 -4.290522,-3.67798c-1.437346,-1.649248 -2.693618,-4.127172 -1.497947,-5.872594c0.763163,-1.089045 2.199692,-2.71201 3.603455,-3.110818c-0.99092,-0.127534 -3.183316,-0.813839 -3.998653,-2.720098c-0.261698,-0.604381 -0.358777,-1.544082 -0.425341,-2.216425c-0.108303,-1.110012 -0.590246,-2.38532 -0.990318,-3.478297c-0.321611,-0.999243 -0.890042,-2.278497 -1.484932,-3.37173c-0.623442,-1.147232 -1.134568,-3.044227 -0.049593,-3.978355c0.379002,-0.326358 1.552542,-0.707001 1.7978,-0.734144c0.857319,-0.096204 2.025341,0.346226 2.892407,0.611656c0.568058,0.175342 3.060551,1.111157 3.749855,1.626429c4.544222,3.396915 7.775498,7.628229 9.874771,11.526587z" stroke-width="1.5" fill="#ffffff"/>
        <path id="svg_5" d="m106.628449,-104.329562c0.005401,0.003589 0.016831,-0.003209 0.022384,-0.004049c-0.00625,-0.008159 0.017291,-0.047907 0.009166,-0.062342c-0.0025,-0.032635 -0.06578,-0.106302 -0.07375,-0.114851c0.00304,-0.005733 0.037109,-0.000398 0.036092,0.000424c-0.014217,0.0115 0.047975,0.0775 0.055158,0.115682c0.013234,-0.000736 0.030618,-0.005715 0.038333,0.00251c0.007204,-0.007905 0.062794,-0.068306 0.065833,-0.120499c0.000435,-0.007479 0.030606,-0.002746 0.024931,0.001813c-0.009097,0.032496 -0.073252,0.123733 -0.078264,0.127054c0.005833,0.028451 0.003471,0.030401 0.040729,0.052524c0.016767,-0.039536 0.052494,-0.073097 0.085216,-0.106499c0.036948,-0.037504 0.061745,-0.073433 0.102446,-0.112475c0.053579,-0.049595 0.122006,-0.094848 0.184677,-0.121862c0.043597,-0.018792 0.116644,-0.034903 0.127842,-0.035957c0.020745,-0.002022 0.039505,-0.001663 0.064094,0.002005c0.036282,0.005413 0.071709,0.008436 0.109802,0.026391c0.035818,0.023044 0.023436,0.051926 0.016758,0.078979c-0.055166,0.06109 -0.111669,0.134253 -0.129464,0.209102c-0.005983,0.027715 -0.01674,0.075474 -0.042129,0.105474c-0.040817,0.046146 -0.090771,0.044439 -0.137706,0.050695c0.028598,0.008223 0.059616,0.035071 0.087444,0.06205c0.043409,0.042435 0.064561,0.085953 0.053351,0.13333c-0.002386,0.009937 -0.043776,0.085309 -0.064021,0.108959c-0.076839,0.090055 -0.181449,0.151128 -0.265762,0.141216c-0.030362,-0.003569 -0.114112,-0.067584 -0.12917,-0.086474c-0.037379,-0.046891 -0.068082,-0.103451 -0.069344,-0.152832c-0.004646,-0.004662 -0.010329,-0.005006 -0.007823,-0.011243c-0.004656,0.014282 -0.019441,0.060791 -0.041329,0.090194c-0.031644,0.042508 -0.060611,-0.021035 -0.072336,-0.072566c-0.004019,-0.017665 -0.001069,-0.020723 -0.005757,-0.031286c-0.022167,0.008446 -0.010495,0.032935 -0.019195,0.052718c-0.007375,0.017227 -0.040406,0.069846 -0.049527,0.081842c-0.029157,0.038345 -0.094628,0.09637 -0.14211,0.110293c-0.044068,0.012923 -0.081418,0.01111 -0.116836,-0.007132c-0.045682,-0.023527 -0.110062,-0.078169 -0.149669,-0.122599c-0.05014,-0.054975 -0.093963,-0.137572 -0.052254,-0.195752c0.026622,-0.036301 0.076733,-0.0904 0.125702,-0.103694c-0.034567,-0.004251 -0.111046,-0.027128 -0.139488,-0.09067c-0.009129,-0.020146 -0.012515,-0.051469 -0.014837,-0.073881c-0.003778,-0.037 -0.02059,-0.07951 -0.034546,-0.115943c-0.011219,-0.033308 -0.031048,-0.07595 -0.0518,-0.112391c-0.021748,-0.038241 -0.039578,-0.101474 -0.00173,-0.132611c0.013221,-0.010879 0.054158,-0.023567 0.062714,-0.024471c0.029906,-0.003207 0.070651,0.011541 0.100898,0.020388c0.019816,0.005845 0.106763,0.037038 0.130809,0.054214c0.158519,0.11323 0.271238,0.254273 0.344469,0.384218z" stroke-width="1.5" stroke="#9feaea" fill="#bae2e2"/>
       </g>
      </svg>
    </div>
  </div>
</div>
<script>{{ source('loader.js') }}</script>