Eu tenho uma api usando Laravel 12. Eu conectei ao banco de dados mysql.
Primeiro eu criei meu model User e configurei:

```

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * User roles defaults values
     */
    public const ROLE_ADMIN = 'admin';

    public const ROLE_LANDLORD = 'landlord';
    public const ROLE_EMPLOY = 'employ';
    public const ROLE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
```

Em seguida criei um model Category:

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'store_id',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
```

Depois configurei a tabela:

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('store_id');
            $table->timestamps();

            /** define forein key */
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

Depois criei e configurei meu controller:

```

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get()
    {
        $categories = Category::orderBy('name')->get();
        return response()->json($categories);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'store_id' => 'required|exists:stores,id',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'store_id' => $request->store_id,
        ]);

        return response()->json(['message' => 'Category added successfully'], 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string',
        ]);

        $category = Category::find($request->id);
        $category->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Category updated successfully'], 200);
    }

    public function delete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
```

Eu configuei minhas rotas:

```

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories/add', [CategoryController::class, 'add']);
    Route::patch('/categories/update', [CategoryController::class, 'update']);
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'delete']);
});
```

Agora eu quero que você adicione respostas de erro a função `add` do controller `Category`.

Quando um usuário não autenticado tentar criar uma categoria, retornar um erro informado disso.

quando um usuário que está tentando criar uma categoria cujo o `store_id` não é seu, também deve retornar um error.

Da forma como está, quando realizo testes no Insomnia, onde executo uma das duas funções retorna `status: 200` e aparece a tela de `welcome` do laravel.
