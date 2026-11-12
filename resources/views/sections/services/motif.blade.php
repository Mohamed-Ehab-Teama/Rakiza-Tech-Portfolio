{{-- The small animated illustration inside a service card (decorative). --}}
@switch($visual)
  @case('app')
    <div class="services__phone">
      <span class="services__phone-notch"></span>
      <div class="services__phone-screen">
        <span class="services__row services__row--bar"></span>
        <span class="services__row services__row--w70"></span>
        <span class="services__row services__row--w90"></span>
        <span class="services__row services__row--w50"></span>
        <span class="services__row services__row--w80"></span>
      </div>
    </div>
    @break
  @case('chart')
    <div class="services__chart">
      @foreach ([42, 66, 38, 82, 55, 94, 48] as $height)
        <span class="services__bar" style="--h:{{ $height }}%"></span>
      @endforeach
    </div>
    @break
  @case('db')
    <div class="services__db">
      @for ($row = 0; $row < 4; $row++)
        <span class="services__db-row"><i class="services__db-key"></i><i class="services__db-cell"></i></span>
      @endfor
      <span class="services__db-pulse"></span>
    </div>
    @break
  @case('cli')
    <div class="services__term">
      <span class="services__term-line"><span class="term-prompt">$</span> <span class="term-out">ci run --deploy</span></span>
      <span class="services__term-line"><span class="term-out">build</span> <span class="term-ok">ok</span></span>
      <span class="services__term-line"><span class="term-out">tests</span> <span class="term-ok">142 passed</span></span>
      <span class="services__term-line"><span class="term-prompt">$</span> <span class="caret"></span></span>
    </div>
    @break
  @default
    <pre class="services__code mono"><code><span class="services__line"><span class="tok-com">// ship with confidence</span></span>
<span class="services__line"><span class="tok-key">export const</span> <span class="tok-fn">build</span> <span class="tok-punc">=</span> <span class="tok-key">async</span> <span class="tok-punc">(</span>src<span class="tok-punc">)</span> <span class="tok-punc">=&gt;</span> <span class="tok-punc">{</span></span>
<span class="services__line">  <span class="tok-key">const</span> out <span class="tok-punc">=</span> <span class="tok-key">await</span> <span class="tok-fn">compile</span><span class="tok-punc">(</span>src<span class="tok-punc">)</span><span class="tok-punc">;</span></span>
<span class="services__line">  <span class="tok-fn">assert</span><span class="tok-punc">(</span>out.tests <span class="tok-punc">===</span> <span class="tok-str">"pass"</span><span class="tok-punc">)</span><span class="tok-punc">;</span></span>
<span class="services__line">  <span class="tok-key">return</span> out.<span class="tok-fn">deploy</span><span class="tok-punc">(</span><span class="tok-num">2</span><span class="tok-punc">)</span><span class="tok-punc">;</span></span>
<span class="services__line"><span class="tok-punc">}</span><span class="services__cursor"></span></span></code></pre>
@endswitch
